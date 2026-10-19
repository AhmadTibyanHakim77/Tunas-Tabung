@extends('layouts.app')
@section('title', 'Riwayat transaksi')
@section('crumb', 'TRANSAKSI')
@section('heading', 'Riwayat transaksi')

@section('content')
<div class="welcome-row">
    <p class="muted">Pantau seluruh setoran dan penarikan yang tercatat.</p>
    <a class="button primary" href="{{ route('transactions.create') }}"><span aria-hidden="true">＋</span> Catat transaksi</a>
</div>

<section class="panel" aria-label="Daftar transaksi">
    <form class="filters transaction-filters" method="GET" action="{{ route('transactions.index') }}">
        <label class="sr-only" for="transaction-search">Cari siswa atau nomor induk</label>
        <input id="transaction-search" name="q" value="{{ request('q') }}" placeholder="Cari siswa atau nomor induk" autocomplete="off">
        <label class="sr-only" for="transaction-type">Jenis transaksi</label>
        <select id="transaction-type" name="type">
            <option value="">Semua jenis</option>
            <option value="deposit" @selected(request('type')==='deposit' )>Setoran</option>
            <option value="withdrawal" @selected(request('type')==='withdrawal' )>Penarikan</option>
        </select>
        <label class="sr-only" for="transaction-student">Pilih siswa</label>
        <select id="transaction-student" name="student_id">
            <option value="">Semua siswa</option>
            @foreach ($students as $student)
            <option value="{{ $student->id }}" @selected(request('student_id')==$student->id)>{{ $student->name }} · {{ $student->student_number }}</option>
            @endforeach
        </select>
        <label class="filter-date">Dari<input type="date" name="date_from" value="{{ request('date_from') }}" aria-label="Tanggal mulai"></label>
        <label class="filter-date">Sampai<input type="date" name="date_to" value="{{ request('date_to') }}" aria-label="Tanggal akhir"></label>
        <button class="button soft" type="submit">Terapkan</button>
        @if (request()->hasAny(['q', 'type', 'student_id', 'date_from', 'date_to']))
        <a class="text-link" href="{{ route('transactions.index') }}">Reset filter</a>
        @endif
    </form>

    <div class="table-wrap" role="region" aria-label="Riwayat transaksi, geser tabel ke samping untuk melihat semua kolom" tabindex="0">
        <table>
            <caption class="sr-only">Daftar seluruh transaksi tabungan</caption>
            <thead>
                <tr>
                    <th scope="col">TANGGAL</th>
                    <th scope="col">SISWA</th>
                    <th scope="col">JENIS</th>
                    <th scope="col">KETERANGAN</th>
                    <th scope="col">PENGELOLA</th>
                    <th scope="col" class="right">NOMINAL</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($transactions as $transaction)
                <tr>
                    <td>{{ $transaction->transaction_date->format('d M Y') }}</td>
                    <td><a class="text-link" href="{{ route('students.show', $transaction->student) }}">{{ $transaction->student->name }}<small class="block-muted">{{ $transaction->student->student_number }} · Kelas {{ $transaction->student->class }}</small></a></td>
                    <td><span class="pill {{ $transaction->type }}">{{ $transaction->type === 'deposit' ? 'Setoran' : 'Penarikan' }}</span></td>
                    <td>{{ $transaction->description ?: '—' }}</td>
                    <td>{{ $transaction->user?->name ?? 'Pengelola' }}</td>
                    <td class="right amount {{ $transaction->type }}">{{ $transaction->type === 'deposit' ? '+' : '−' }} Rp {{ number_format($transaction->amount, 0, ',', '.') }}</td>
                </tr>
                @empty
                <tr>
                    <td colspan="6">
                        <div class="empty-state">
                            <strong>{{ request()->hasAny(['q', 'type', 'student_id', 'date_from', 'date_to']) ? 'Transaksi tidak ditemukan' : 'Belum ada transaksi' }}</strong>
                            <p>{{ request()->hasAny(['q', 'type', 'student_id', 'date_from', 'date_to']) ? 'Sesuaikan filter untuk melihat transaksi lainnya.' : 'Catat transaksi pertama untuk mulai membangun riwayat tabungan.' }}</p>
                            @unless (request()->hasAny(['q', 'type', 'student_id', 'date_from', 'date_to']))
                            <a class="text-link" href="{{ route('transactions.create') }}">Catat transaksi pertama</a>
                            @endunless
                        </div>
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    @if ($transactions->hasPages())
    <div class="pagination">{{ $transactions->links() }}</div>
    @endif
</section>
@endsection