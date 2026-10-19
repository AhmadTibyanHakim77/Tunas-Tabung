<?php

namespace Database\Seeders;

use App\Models\SavingTransaction;
use App\Models\Student;
use App\Models\User;
use Database\Factories\StudentFactory;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $teacher = User::firstOrCreate(
            ['email' => 'test@example.com'],
            ['name' => 'Guru Pengelola', 'password' => 'password', 'role' => 'teacher'],
        );

        if (Student::exists()) {
            return;
        }

        foreach (StudentFactory::PROFILES as $index => $profile) {
            $student = Student::factory()->create([
                'student_number' => sprintf('2026%03d', $index + 1),
                ...$profile,
                'class' => ($index % 6 + 1).'A',
            ]);

            foreach (range(5, 0) as $monthsAgo) {
                $month = now()->startOfMonth()->subMonths($monthsAgo);
                $transactionDay = min(5 + ($index % 20), $month->daysInMonth);

                if ($monthsAgo === 0) {
                    $transactionDay = min($transactionDay, now()->day);
                }

                $transactionDate = $month->copy()->day($transactionDay)->toDateString();
                $deposit = 10000 + ($index % 4) * 5000;

                SavingTransaction::create([
                    'student_id' => $student->id,
                    'user_id' => $teacher->id,
                    'type' => 'deposit',
                    'amount' => $deposit,
                    'description' => 'Setoran tabungan bulanan',
                    'transaction_date' => $transactionDate,
                ]);

                if (($index + $monthsAgo) % 3 === 0) {
                    SavingTransaction::create([
                        'student_id' => $student->id,
                        'user_id' => $teacher->id,
                        'type' => 'withdrawal',
                        'amount' => 5000,
                        'description' => 'Penarikan kebutuhan sekolah',
                        'transaction_date' => $transactionDate,
                    ]);
                }
            }
        }
    }
}
