<?php

namespace App\Http\Controllers;

use App\Models\SavingTransaction;
use App\Models\Student;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;
use Illuminate\View\View;

class SavingTransactionController extends Controller
{
    public function index(Request $request): View
    {
        $filters = $request->validate([
            'q' => ['nullable', 'string', 'max:100'],
            'type' => ['nullable', 'in:deposit,withdrawal'],
            'student_id' => ['nullable', 'integer', 'exists:students,id'],
            'date_from' => ['nullable', 'date'],
            'date_to' => ['nullable', 'date', 'after_or_equal:date_from'],
        ]);

        $transactions = SavingTransaction::with(['student', 'user'])
            ->when($filters['q'] ?? null, fn ($query, $term) => $query->whereHas('student', fn ($studentQuery) => $studentQuery->where('name', 'like', '%'.$term.'%')->orWhere('student_number', 'like', '%'.$term.'%')))
            ->when($filters['type'] ?? null, fn ($query, $type) => $query->where('type', $type))
            ->when($filters['student_id'] ?? null, fn ($query, $studentId) => $query->where('student_id', $studentId))
            ->when($filters['date_from'] ?? null, fn ($query, $date) => $query->whereDate('transaction_date', '>=', $date))
            ->when($filters['date_to'] ?? null, fn ($query, $date) => $query->whereDate('transaction_date', '<=', $date))
            ->latest('transaction_date')->latest('id')->paginate(15)->withQueryString();

        return view('transactions.index', ['transactions' => $transactions, 'students' => Student::orderBy('name')->get(['id', 'name', 'student_number'])]);
    }

    public function create(): View
    {
        return view('transactions.create', ['students' => Student::where('is_active', true)->orderBy('name')->get(['id', 'name', 'student_number'])]);
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'student_id' => ['required', 'integer', 'exists:students,id'],
            'type' => ['required', 'in:deposit,withdrawal'],
            'amount' => ['required', 'integer', 'min:1', 'max:1000000000'],
            'transaction_date' => ['required', 'date', 'before_or_equal:today'],
            'description' => ['nullable', 'string', 'max:1000'],
        ]);

        DB::transaction(function () use ($validated, $request): void {
            $student = Student::query()->lockForUpdate()->findOrFail($validated['student_id']);
            abort_unless($student->is_active, 422, 'Siswa sudah tidak aktif.');

            if ($validated['type'] === 'withdrawal') {
                $balance = (int) $student->transactions()->where('type', 'deposit')->sum('amount')
                    - (int) $student->transactions()->where('type', 'withdrawal')->sum('amount');

                if ($validated['amount'] > $balance) {
                    throw ValidationException::withMessages(['amount' => 'Saldo tidak mencukupi untuk penarikan ini.']);
                }
            }

            SavingTransaction::create([...$validated, 'user_id' => $request->user()?->id]);
        }, attempts: 3);

        return redirect()->route('transactions.index')->with('success', 'Transaksi berhasil dicatat.');
    }
}
