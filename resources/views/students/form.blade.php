@extends('layouts.app')
@section('title', $student->exists ? 'Edit siswa' : 'Tambah siswa')
@section('crumb', 'DATA SISWA')
@section('heading', $student->exists ? 'Edit data siswa' : 'Tambah siswa')

@section('content')
<p class="muted form-intro">Lengkapi informasi siswa untuk mengelola tabungannya.</p>

<section class="panel form-panel">
    <form method="POST" action="{{ $student->exists ? route('students.update', $student) : route('students.store') }}">
        @csrf
        @if ($student->exists)
        @method('PUT')
        @endif

        <div class="form-grid">
            <label>
                Nomor induk siswa
                <input name="student_number" value="{{ old('student_number', $student->student_number) }}" required maxlength="50" placeholder="Contoh: 2026001" aria-invalid="{{ $errors->has('student_number') ? 'true' : 'false' }}">
                @error('student_number')<span class="field-error">{{ $message }}</span>@enderror
            </label>
            <label>
                Nama lengkap
                <input name="name" value="{{ old('name', $student->name) }}" required maxlength="255" placeholder="Nama lengkap siswa" aria-invalid="{{ $errors->has('name') ? 'true' : 'false' }}">
                @error('name')<span class="field-error">{{ $message }}</span>@enderror
            </label>
            <label>
                Kelas
                <input name="class" value="{{ old('class', $student->class) }}" required maxlength="50" placeholder="Contoh: 3A" aria-invalid="{{ $errors->has('class') ? 'true' : 'false' }}">
                @error('class')<span class="field-error">{{ $message }}</span>@enderror
            </label>
            <label>
                Jenis kelamin <span class="optional-label">Opsional</span>
                <select name="gender" aria-invalid="{{ $errors->has('gender') ? 'true' : 'false' }}">
                    <option value="">Pilih jenis kelamin</option>
                    <option value="Laki-laki" @selected(old('gender', $student->gender) === 'Laki-laki')>Laki-laki</option>
                    <option value="Perempuan" @selected(old('gender', $student->gender) === 'Perempuan')>Perempuan</option>
                </select>
                @error('gender')<span class="field-error">{{ $message }}</span>@enderror
            </label>
            <label>
                Tanggal lahir <span class="optional-label">Opsional</span>
                <input type="date" name="date_of_birth" value="{{ old('date_of_birth', $student->date_of_birth?->format('Y-m-d')) }}" max="{{ now()->toDateString() }}" aria-invalid="{{ $errors->has('date_of_birth') ? 'true' : 'false' }}">
                @error('date_of_birth')<span class="field-error">{{ $message }}</span>@enderror
            </label>
            @if ($student->exists)
            <label>
                Status
                <select name="is_active" aria-invalid="{{ $errors->has('is_active') ? 'true' : 'false' }}">
                    <option value="1" @selected((string) old('is_active', (int) $student->is_active) === '1')>Aktif</option>
                    <option value="0" @selected((string) old('is_active', (int) $student->is_active) === '0')>Nonaktif</option>
                </select>
                @error('is_active')<span class="field-error">{{ $message }}</span>@enderror
            </label>
            @endif
        </div>

        <div class="form-actions">
            <a class="button soft" href="{{ $student->exists ? route('students.show', $student) : route('students.index') }}">Batal</a>
            <button class="button primary" type="submit">Simpan data</button>
        </div>
    </form>
</section>
@endsection