<?php

namespace App\Http\Controllers;

use App\Models\SavingTransaction;
use App\Models\Student;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class StudentController extends Controller
{
    public function index(Request $request): View
    {
        $sort = $request->validate(['sort' => ['nullable', 'in:class,name,balance']])['sort'] ?? 'class';

        $studentSavings = SavingTransaction::query()
            ->select('student_id')
            ->selectRaw("COALESCE(SUM(CASE WHEN type = 'deposit' THEN amount ELSE 0 END), 0) as deposits")
            ->selectRaw("COALESCE(SUM(CASE WHEN type = 'withdrawal' THEN amount ELSE 0 END), 0) as withdrawals")
            ->selectRaw('COUNT(*) as transaction_count')
            ->groupBy('student_id');

        $studentsQuery = Student::query()
            ->leftJoinSub($studentSavings, 'student_savings', fn ($join) => $join->on('students.id', '=', 'student_savings.student_id'))
            ->select('students.*')
            ->selectRaw('COALESCE(student_savings.deposits, 0) as deposits')
            ->selectRaw('COALESCE(student_savings.withdrawals, 0) as withdrawals')
            ->selectRaw('COALESCE(student_savings.transaction_count, 0) as transaction_count')
            ->when($request->string('q')->trim()->isNotEmpty(), fn ($query) => $query->where(fn ($query) => $query->where('name', 'like', '%'.$request->string('q')->trim().'%')->orWhere('student_number', 'like', '%'.$request->string('q')->trim().'%')))
            ->when($request->filled('class'), fn ($query) => $query->where('class', $request->string('class')));

        match ($sort) {
            'name' => $studentsQuery->orderBy('students.name')->orderBy('students.student_number'),
            'balance' => $studentsQuery->orderByRaw('COALESCE(student_savings.deposits, 0) - COALESCE(student_savings.withdrawals, 0) DESC')->orderBy('students.name'),
            default => $studentsQuery->orderBy('students.class')->orderBy('students.name'),
        };

        $students = $studentsQuery->paginate(12)->withQueryString();

        return view('students.index', ['students' => $students, 'classes' => Student::query()->select('class')->distinct()->orderBy('class')->pluck('class')]);
    }

    public function create(): View
    {
        return view('students.form', ['student' => new Student]);
    }

    public function store(Request $request): RedirectResponse
    {
        Student::create($this->validated($request));

        return redirect()->route('students.index')->with('success', 'Data siswa berhasil ditambahkan.');
    }

    public function show(Student $student): View
    {
        $totals = $student->transactions()->selectRaw("COALESCE(SUM(CASE WHEN type = 'deposit' THEN amount ELSE 0 END), 0) as deposits")
            ->selectRaw("COALESCE(SUM(CASE WHEN type = 'withdrawal' THEN amount ELSE 0 END), 0) as withdrawals")->first();

        return view('students.show', ['student' => $student, 'transactions' => $student->transactions()->with('user')->latest('transaction_date')->latest('id')->paginate(15), 'deposits' => (int) $totals->deposits, 'withdrawals' => (int) $totals->withdrawals]);
    }

    public function edit(Student $student): View
    {
        return view('students.form', ['student' => $student]);
    }

    public function update(Request $request, Student $student): RedirectResponse
    {
        $student->update($this->validated($request, $student));

        return redirect()->route('students.show', $student)->with('success', 'Data siswa berhasil diperbarui.');
    }

    public function destroy(Student $student): RedirectResponse
    {
        if ($student->transactions()->exists()) {
            $student->update(['is_active' => false]);
        } else {
            $student->delete();
        }

        return redirect()->route('students.index')->with('success', 'Siswa berhasil dinonaktifkan.');
    }

    private function validated(Request $request, ?Student $student = null): array
    {
        return $request->validate([
            'student_number' => ['required', 'string', 'max:50', 'unique:students,student_number,'.$student?->id],
            'name' => ['required', 'string', 'max:255'],
            'class' => ['required', 'string', 'max:50'],
            'gender' => ['nullable', 'in:Laki-laki,Perempuan'],
            'date_of_birth' => ['nullable', 'date', 'before_or_equal:today'],
            'is_active' => ['sometimes', 'boolean'],
        ]);
    }
}
