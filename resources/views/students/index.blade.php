@extends('layouts.app')
@section('title', 'Data siswa')
@section('crumb', 'DATA SISWA')
@section('heading', 'Data siswa')

@section('content')
<div class="welcome-row">
    <p class="muted">Kelola identitas, status, dan saldo tabungan siswa.</p>
    <a class="button primary" href="{{ route('students.create') }}"><span aria-hidden="true">＋</span> Tambah siswa</a>
</div>

<section class="panel" aria-label="Daftar siswa">
    <form class="filters" method="GET" action="{{ route('students.index') }}">
        <label class="sr-only" for="student-search">Cari nama atau nomor induk siswa</label>
        <input id="student-search" name="q" value="{{ request('q') }}" placeholder="Cari nama atau nomor induk siswa" autocomplete="off">
        <label class="sr-only" for="student-class">Filter berdasarkan kelas</label>
        <select id="student-class" name="class">
            <option value="">Semua kelas</option>
            @foreach ($classes as $class)
            <option value="{{ $class }}" @selected(request('class')===$class)>Kelas {{ $class }}</option>
            @endforeach
        </select>
        <label class="sr-only" for="student-sort">Urutkan siswa</label>
        <select id="student-sort" name="sort">
            <option value="class" @selected(request('sort', 'class' )==='class' )>Urutkan: kelas</option>
            <option value="name" @selected(request('sort')==='name' )>Urutkan: nama</option>
            <option value="balance" @selected(request('sort')==='balance' )>Urutkan: saldo tertinggi</option>
        </select>
        <button class="button soft" type="submit">Terapkan</button>
        @if (request()->hasAny(['q', 'class', 'sort']) && request('sort') !== 'class')
        <a class="text-link" href="{{ route('students.index') }}">Reset filter</a>
        @elseif (request()->hasAny(['q', 'class']))
        <a class="text-link" href="{{ route('students.index') }}">Reset filter</a>
        @endif
    </form>

    <div class="table-wrap" role="region" aria-label="Data siswa, geser tabel ke samping untuk melihat semua kolom" tabindex="0">
        <table>
            <caption class="sr-only">Daftar siswa dan saldo tabungannya</caption>
            <thead>
                <tr>
                    <th scope="col">SISWA</th>
                    <th scope="col">NOMOR INDUK</th>
                    <th scope="col">KELAS</th>
                    <th scope="col">STATUS</th>
                    <th scope="col" class="right">TRANSAKSI</th>
                    <th scope="col" class="right">SALDO</th>
                    <th scope="col">AKSI</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($students as $student)
                <tr>
                    <td><a class="student-link" href="{{ route('students.show', $student) }}"><span class="mini-avatar" aria-hidden="true">{{ strtoupper(substr($student->name, 0, 1)) }}</span><strong>{{ $student->name }}</strong></a></td>
                    <td>{{ $student->student_number }}</td>
                    <td>{{ $student->class }}</td>
                    <td><span class="pill {{ $student->is_active ? 'active-pill' : 'inactive-pill' }}">{{ $student->is_active ? 'Aktif' : 'Nonaktif' }}</span></td>
                    <td class="right">{{ number_format((int) $student->transaction_count, 0, ',', '.') }}</td>
                    <td class="right amount">Rp {{ number_format((int) $student->deposits - (int) $student->withdrawals, 0, ',', '.') }}</td>
                    <td>
                        <div class="row-actions"><a class="text-link" href="{{ route('students.show', $student) }}">Lihat</a><a class="text-link" href="{{ route('students.edit', $student) }}">Edit</a></div>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="7">
                        <div class="empty-state">
                            <span class="empty-illustration" aria-hidden="true">＋</span>
                            <strong>{{ request()->hasAny(['q', 'class']) ? 'Siswa tidak ditemukan' : 'Belum ada data siswa' }}</strong>
                            <p>{{ request()->hasAny(['q', 'class']) ? 'Ubah kata kunci atau filter kelas, lalu coba lagi.' : 'Tambahkan siswa untuk mulai mencatat tabungan.' }}</p>
                            @unless (request()->hasAny(['q', 'class']))
                            <a class="text-link" href="{{ route('students.create') }}">Tambah siswa pertama</a>
                            @endunless
                        </div>
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    @if ($students->hasPages())
    <div class="pagination">{{ $students->links() }}</div>
    @endif
</section>
@endsection