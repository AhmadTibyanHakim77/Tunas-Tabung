<?php

namespace Tests\Feature;

use App\Models\SavingTransaction;
use App\Models\Student;
use Database\Seeders\DatabaseSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class SavingsWorkflowTest extends TestCase
{
    use RefreshDatabase;

    public function test_dashboard_and_core_pages_are_available_without_login(): void
    {
        $this->get('/')->assertRedirect('/dashboard');
        $this->get('/dashboard')->assertOk()->assertSee('Total saldo tabungan')->assertSee('Belum ada data saldo');
        $this->get('/students')->assertOk();
        $this->get('/transactions')->assertOk();
        $this->get('/transactions/create')->assertOk()->assertSee('Belum ada siswa aktif');
        $this->get('/login')->assertNotFound();
    }

    public function test_dashboard_analytics_are_aggregated_by_month_from_transactions(): void
    {
        $student = Student::factory()->create();
        Student::factory()->create();
        $currentMonth = now()->startOfMonth()->addDays(2)->toDateString();
        $previousMonth = now()->startOfMonth()->subMonth()->addDays(9)->toDateString();

        SavingTransaction::create(['student_id' => $student->id, 'user_id' => null, 'type' => 'deposit', 'amount' => 10000, 'transaction_date' => $previousMonth]);
        SavingTransaction::create(['student_id' => $student->id, 'user_id' => null, 'type' => 'deposit', 'amount' => 20000, 'transaction_date' => $currentMonth]);
        SavingTransaction::create(['student_id' => $student->id, 'user_id' => null, 'type' => 'withdrawal', 'amount' => 5000, 'transaction_date' => $currentMonth]);

        $response = $this->get('/dashboard')->assertOk();
        $chartData = $response->viewData('chartData');
        $currentMonthIndex = count($chartData['labels']) - 1;
        $previousMonthIndex = $currentMonthIndex - 1;

        $this->assertSame(2, $response->viewData('studentTotal'));
        $this->assertSame(2, $response->viewData('studentCount'));
        $this->assertCount(6, $chartData['labels']);
        $this->assertSame(10000, $chartData['deposits'][$previousMonthIndex]);
        $this->assertSame(20000, $chartData['deposits'][$currentMonthIndex]);
        $this->assertSame(5000, $chartData['withdrawals'][$currentMonthIndex]);
        $this->assertSame(25000, $chartData['balance'][$currentMonthIndex]);
        $this->assertSame(2, $chartData['transactions'][$currentMonthIndex]);
        $this->assertSame([2, 1], $chartData['transactionTypes']);
        $this->assertSame(100.0, $response->viewData('depositTrend'));
        $this->assertNull($response->viewData('withdrawalTrend'));
        $this->assertCount(2, $response->viewData('topStudents'));
        $this->assertSame($student->id, $response->viewData('topStudents')->first()->id);
        $this->assertSame(25000, (int) $response->viewData('topStudents')->first()->balance);
        $this->assertTrue($chartData['hasActivity']);
    }

    public function test_students_can_be_sorted_by_name_and_net_balance(): void
    {
        $highestBalance = Student::factory()->create(['student_number' => 'S-201', 'name' => 'Rina Aulia', 'class' => '4A']);
        $lowestBalance = Student::factory()->create(['student_number' => 'S-202', 'name' => 'Ahmad Rizki', 'class' => '2A']);

        SavingTransaction::create(['student_id' => $highestBalance->id, 'user_id' => null, 'type' => 'deposit', 'amount' => 30000, 'transaction_date' => now()->toDateString()]);
        SavingTransaction::create(['student_id' => $lowestBalance->id, 'user_id' => null, 'type' => 'deposit', 'amount' => 10000, 'transaction_date' => now()->toDateString()]);

        $balanceOrder = $this->get('/students?sort=balance')->assertOk()->viewData('students')->getCollection()->pluck('id')->all();
        $nameOrder = $this->get('/students?sort=name')->assertOk()->viewData('students')->getCollection()->pluck('id')->all();

        $this->assertSame([$highestBalance->id, $lowestBalance->id], $balanceOrder);
        $this->assertSame([$lowestBalance->id, $highestBalance->id], $nameOrder);
        $this->get('/students?sort=invalid')->assertSessionHasErrors('sort');
    }

    public function test_demo_seeder_creates_indonesian_students_with_consistent_monthly_savings(): void
    {
        $this->seed(DatabaseSeeder::class);

        $this->assertDatabaseCount('students', 24);
        $this->assertDatabaseCount('saving_transactions', 192);
        $this->assertDatabaseHas('students', ['name' => 'Ahmad Fadillah', 'gender' => 'Laki-laki']);
        $this->assertDatabaseHas('students', ['name' => 'Aisyah Putri', 'gender' => 'Perempuan']);

        $totals = SavingTransaction::query()
            ->selectRaw("SUM(CASE WHEN type = 'deposit' THEN amount ELSE 0 END) as deposits")
            ->selectRaw("SUM(CASE WHEN type = 'withdrawal' THEN amount ELSE 0 END) as withdrawals")
            ->first();

        $this->assertSame(2520000, (int) $totals->deposits);
        $this->assertSame(240000, (int) $totals->withdrawals);
        $this->assertSame(2280000, (int) $totals->deposits - (int) $totals->withdrawals);
    }

    public function test_guest_can_record_deposit_and_withdrawal_without_negative_balance(): void
    {
        $student = Student::factory()->create();

        $this->post('/transactions', ['student_id' => $student->id, 'type' => 'deposit', 'amount' => 25000, 'transaction_date' => now()->toDateString(), 'description' => 'Tabungan awal'])
            ->assertRedirect('/transactions');
        $this->post('/transactions', ['student_id' => $student->id, 'type' => 'withdrawal', 'amount' => 10000, 'transaction_date' => now()->toDateString(), 'description' => 'Pengambilan'])
            ->assertRedirect('/transactions');

        $this->assertDatabaseCount('saving_transactions', 2);
        $this->assertDatabaseHas('saving_transactions', ['student_id' => $student->id, 'type' => 'deposit', 'user_id' => null]);
        $this->get('/transactions')->assertOk()->assertSee('Setoran')->assertSee('Penarikan');
        $this->get('/transactions?q='.$student->student_number.'&type=deposit&date_from='.now()->toDateString().'&date_to='.now()->toDateString())
            ->assertOk()->assertSee('Tabungan awal')->assertDontSee('Pengambilan');
        $this->assertSame(15000, (int) $student->transactions()->where('type', 'deposit')->sum('amount') - (int) $student->transactions()->where('type', 'withdrawal')->sum('amount'));

        $this->from('/transactions/create')->post('/transactions', ['student_id' => $student->id, 'type' => 'withdrawal', 'amount' => 20000, 'transaction_date' => now()->toDateString()])->assertSessionHasErrors('amount');
        $this->assertDatabaseCount('saving_transactions', 2);
    }

    public function test_student_crud_and_rupiah_detail_are_available_without_login(): void
    {
        $this->get('/students/create')->assertOk();
        $this->post('/students', ['student_number' => 'S-001', 'name' => 'Budi Santoso', 'class' => '3A'])->assertRedirect('/students');
        $student = Student::firstOrFail();
        $this->get('/students/'.$student->id.'/edit')->assertOk();
        $this->put('/students/'.$student->id, ['student_number' => 'S-001', 'name' => 'Budi Santoso', 'class' => '4A'])->assertRedirect('/students/'.$student->id);
        SavingTransaction::create(['student_id' => $student->id, 'user_id' => null, 'type' => 'deposit', 'amount' => 10000, 'transaction_date' => now()->toDateString()]);

        $this->get('/students/'.$student->id)->assertOk()->assertSee('Rp 10.000')->assertSee('Budi Santoso')->assertSee('Kelas 4A');
        $this->delete('/students/'.$student->id)->assertRedirect('/students');
        $this->assertDatabaseHas('students', ['id' => $student->id, 'is_active' => false]);
        $this->assertDatabaseCount('saving_transactions', 1);
    }
}
