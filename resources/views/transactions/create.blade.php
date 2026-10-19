@extends('layouts.app')
@section('title', 'Catat transaksi')
@section('crumb', 'TRANSAKSI')
@section('heading', 'Catat transaksi')

@section('content')
<p class="muted form-intro">Catat setoran atau penarikan tabungan siswa.</p>

@if ($students->isEmpty())
<div class="notice info" role="status"><span aria-hidden="true">i</span>
    <div>Belum ada siswa aktif. Tambahkan siswa sebelum mencatat transaksi.</div>
</div>
<a class="button primary" href="{{ route('students.create') }}">Tambah siswa</a>
@else
<section class="panel form-panel">
    <form method="POST" action="{{ route('transactions.store') }}">
        @csrf
        <div class="form-grid">
            <label>
                Siswa
                <select name="student_id" required aria-invalid="{{ $errors->has('student_id') ? 'true' : 'false' }}">
                    <option value="">Pilih siswa</option>
                    @foreach ($students as $student)
                    <option value="{{ $student->id }}" @selected(old('student_id', request('student_id'))==$student->id)>{{ $student->name }} · {{ $student->student_number }}</option>
                    @endforeach
                </select>
                @error('student_id')<span class="field-error">{{ $message }}</span>@enderror
            </label>
            <label>
                Jenis transaksi
                <select name="type" required aria-invalid="{{ $errors->has('type') ? 'true' : 'false' }}">
                    <option value="deposit" @selected(old('type', 'deposit' )==='deposit' )>Setoran</option>
                    <option value="withdrawal" @selected(old('type')==='withdrawal' )>Penarikan</option>
                </select>
                @error('type')<span class="field-error">{{ $message }}</span>@enderror
            </label>
            <label>
                Nominal (Rupiah)
                <input type="number" min="1" max="1000000000" step="1" name="amount" value="{{ old('amount') }}" placeholder="Contoh: 10000" required inputmode="numeric" aria-invalid="{{ $errors->has('amount') ? 'true' : 'false' }}">
                <small>Masukkan angka bulat tanpa titik atau simbol.</small>
                @error('amount')<span class="field-error">{{ $message }}</span>@enderror
            </label>
            <label>
                Tanggal transaksi
                <input type="date" name="transaction_date" value="{{ old('transaction_date', now()->toDateString()) }}" max="{{ now()->toDateString() }}" required aria-invalid="{{ $errors->has('transaction_date') ? 'true' : 'false' }}">
                @error('transaction_date')<span class="field-error">{{ $message }}</span>@enderror
            </label>
            <label class="span-two">
                Keterangan <span class="optional-label">Opsional</span>
                <textarea name="description" rows="3" maxlength="1000" placeholder="Contoh: Tabungan mingguan" aria-invalid="{{ $errors->has('description') ? 'true' : 'false' }}">{{ old('description') }}</textarea>
                @error('description')<span class="field-error">{{ $message }}</span>@enderror
            </label>
        </div>
        <div class="form-actions">
            <a class="button soft" href="{{ route('transactions.index') }}">Batal</a>
            <button class="button primary" type="submit">Simpan transaksi</button>
        </div>
    </form>
</section>
@endif
@endsection