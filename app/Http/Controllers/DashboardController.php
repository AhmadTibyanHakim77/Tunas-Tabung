<?php

namespace App\Http\Controllers;

use App\Models\SavingTransaction;
use App\Models\Student;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class DashboardController extends Controller
{
    public function __invoke(): View
    {
        $startOfPeriod = now()->startOfMonth()->subMonths(5);
        $endOfPeriod = now()->endOfMonth();
        $monthExpression = match (DB::connection()->getDriverName()) {
            'sqlite' => "strftime('%Y-%m', transaction_date)",
            'mysql' => "DATE_FORMAT(transaction_date, '%Y-%m')",
            'pgsql' => "TO_CHAR(transaction_date, 'YYYY-MM')",
            default => throw new \LogicException('The dashboard analytics database driver is not supported.'),
        };

        $summary = SavingTransaction::query()->selectRaw("COALESCE(SUM(CASE WHEN type = 'deposit' THEN amount ELSE 0 END), 0) as deposits")
            ->selectRaw("COALESCE(SUM(CASE WHEN type = 'withdrawal' THEN amount ELSE 0 END), 0) as withdrawals")
            ->selectRaw("SUM(CASE WHEN type = 'deposit' THEN 1 ELSE 0 END) as deposit_count")
            ->selectRaw("SUM(CASE WHEN type = 'withdrawal' THEN 1 ELSE 0 END) as withdrawal_count")
            ->selectRaw('COUNT(*) as transaction_count')
            ->first();

        $openingSummary = SavingTransaction::query()
            ->where('transaction_date', '<', $startOfPeriod->toDateString())
            ->selectRaw("COALESCE(SUM(CASE WHEN type = 'deposit' THEN amount ELSE 0 END), 0) as deposits")
            ->selectRaw("COALESCE(SUM(CASE WHEN type = 'withdrawal' THEN amount ELSE 0 END), 0) as withdrawals")
            ->first();

        $monthlyTransactions = SavingTransaction::query()
            ->whereBetween('transaction_date', [$startOfPeriod->toDateString(), $endOfPeriod->toDateString()])
            ->selectRaw($monthExpression.' as month')
            ->selectRaw('COUNT(*) as transaction_count')
            ->selectRaw("COALESCE(SUM(CASE WHEN type = 'deposit' THEN amount ELSE 0 END), 0) as deposits")
            ->selectRaw("COALESCE(SUM(CASE WHEN type = 'withdrawal' THEN amount ELSE 0 END), 0) as withdrawals")
            ->groupByRaw($monthExpression)
            ->get()
            ->keyBy('month');

        $runningBalance = (int) $openingSummary->deposits - (int) $openingSummary->withdrawals;
        $monthlyData = [];

        foreach (range(0, 5) as $monthOffset) {
            $month = $startOfPeriod->copy()->addMonths($monthOffset);
            $monthTotals = $monthlyTransactions->get($month->format('Y-m'));
            $deposits = (int) ($monthTotals->deposits ?? 0);
            $withdrawals = (int) ($monthTotals->withdrawals ?? 0);
            $runningBalance += $deposits - $withdrawals;

            $monthlyData[] = [
                'label' => $month->locale('id')->translatedFormat('M'),
                'balance' => $runningBalance,
                'deposits' => $deposits,
                'withdrawals' => $withdrawals,
                'transactions' => (int) ($monthTotals->transaction_count ?? 0),
            ];
        }

        $currentMonthData = $monthlyData[array_key_last($monthlyData)];
        $previousMonthData = $monthlyData[array_key_last($monthlyData) - 1];

        $topStudents = Student::query()
            ->where('students.is_active', true)
            ->leftJoin('saving_transactions', 'students.id', '=', 'saving_transactions.student_id')
            ->select(['students.id', 'students.name', 'students.student_number', 'students.class'])
            ->selectRaw("COALESCE(SUM(CASE WHEN saving_transactions.type = 'deposit' THEN saving_transactions.amount WHEN saving_transactions.type = 'withdrawal' THEN -saving_transactions.amount ELSE 0 END), 0) as balance")
            ->selectRaw('COUNT(saving_transactions.id) as transaction_count')
            ->groupBy('students.id', 'students.name', 'students.student_number', 'students.class')
            ->orderByDesc('balance')
            ->orderBy('students.name')
            ->limit(5)
            ->get();

        return view('dashboard', [
            'studentTotal' => Student::count(),
            'studentCount' => Student::where('is_active', true)->count(),
            'transactionCount' => (int) $summary->transaction_count,
            'depositTotal' => (int) $summary->deposits,
            'withdrawalTotal' => (int) $summary->withdrawals,
            'balanceTotal' => (int) $summary->deposits - (int) $summary->withdrawals,
            'depositTrend' => $this->percentageChange($currentMonthData['deposits'], $previousMonthData['deposits']),
            'withdrawalTrend' => $this->percentageChange($currentMonthData['withdrawals'], $previousMonthData['withdrawals']),
            'transactionTrend' => $this->percentageChange($currentMonthData['transactions'], $previousMonthData['transactions']),
            'topStudents' => $topStudents,
            'topStudentBalance' => max(0, (int) ($topStudents->first()?->balance ?? 0)),
            'chartData' => [
                'labels' => array_column($monthlyData, 'label'),
                'balance' => array_column($monthlyData, 'balance'),
                'deposits' => array_column($monthlyData, 'deposits'),
                'withdrawals' => array_column($monthlyData, 'withdrawals'),
                'transactions' => array_column($monthlyData, 'transactions'),
                'transactionTypes' => [(int) $summary->deposit_count, (int) $summary->withdrawal_count],
                'hasActivity' => $monthlyTransactions->isNotEmpty(),
            ],
            'recentTransactions' => SavingTransaction::with(['student', 'user'])->latest('transaction_date')->latest('id')->limit(8)->get(),
        ]);
    }

    private function percentageChange(int $currentValue, int $previousValue): ?float
    {
        if ($previousValue === 0) {
            return null;
        }

        return round((($currentValue - $previousValue) / $previousValue) * 100, 1);
    }
}
