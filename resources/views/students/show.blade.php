@extends('layouts.app')
@section('title', $student->name)
@section('crumb', 'DATA SISWA / DETAIL')
@section('heading', 'Detail siswa')

@section('content')
<div class="welcome-row">
    <p class="muted">Profil dan riwayat tabungan siswa.</p>
    <div class="actions">
        <a class="button soft" href="{{ route('students.edit', $student) }}">Edit siswa</a>
        <a class="button primary" href="{{ route('transactions.create', ['student_id' => $student->id]) }}"><span aria-hidden="true">＋</span> Transaksi</a>
    </div>
</div>

<section class="profile-card" aria-label="Profil {{ $student->name }}">
    <div class="profile-avatar" aria-hidden="true">{{ strtoupper(substr($student->name, 0, 1)) }}</div>
    <div class="profile-info">
        <span class="pill {{ $student->is_active ? 'active-pill' : 'inactive-pill' }}">{{ $student->is_active ? 'Aktif' : 'Nonaktif' }}</span>
        <h2>{{ $student->name }}</h2>
        <p>{{ $student->student_number }} <span aria-hidden="true">·</span> Kelas {{ $student->class }}</p>
        <small>{{ $student->gender ?? 'Jenis kelamin belum diisi' }} @if ($student->date_of_birth) · Lahir {{ $student->date_of_birth->format('d M Y') }} @endif</small>
    </div>
    <div class="balance-box"><small>SALDO SAAT INI</small><strong>Rp {{ number_format($deposits - $withdrawals, 0, ',', '.') }}</strong></div>
</section>

<div class="stats-grid three" aria-label="Ringkasan tabungan siswa">
    <article class="stat-card"><span class="stat-label">Total setoran</span><strong>Rp {{ number_format($deposits, 0, ',', '.') }}</strong></article>
    <article class="stat-card"><span class="stat-label">Total penarikan</span><strong>Rp {{ number_format($withdrawals, 0, ',', '.') }}</strong></article>
    <article class="stat-card featured"><span class="stat-label">Saldo tabungan</span><strong>Rp {{ number_format($deposits - $withdrawals, 0, ',', '.') }}</strong></article>
</div>

<section class="panel" aria-label="Riwayat transaksi {{ $student->name }}">
    <div class="panel-heading">
        <div>
            <h2>Riwayat transaksi</h2>
            <p>Semua aktivitas tabungan {{ $student->name }}.</p>
        </div>
    </div>
    <div class="table-wrap" role="region" aria-label="Riwayat transaksi, geser tabel ke samping untuk melihat semua kolom" tabindex="0">
        <table>
            <caption class="sr-only">Riwayat transaksi tabungan {{ $student->name }}</caption>
            <thead>
                <tr>
                    <th scope="col">TANGGAL</th>
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
                    <td><span class="pill {{ $transaction->type }}">{{ $transaction->type === 'deposit' ? 'Setoran' : 'Penarikan' }}</span></td>
                    <td>{{ $transaction->description ?: '—' }}</td>
                    <td>{{ $transaction->user?->name ?? 'Pengelola' }}</td>
                    <td class="right amount {{ $transaction->type }}">{{ $transaction->type === 'deposit' ? '+' : '−' }} Rp {{ number_format($transaction->amount, 0, ',', '.') }}</td>
                </tr>
                @empty
                <tr>
                    <td colspan="5">
                        <div class="empty-state"><strong>Belum ada transaksi</strong>
                            <p>Catat setoran atau penarikan pertama untuk siswa ini.</p><a class="text-link" href="{{ route('transactions.create', ['student_id' => $student->id]) }}">Catat transaksi</a>
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

<form method="POST" action="{{ route('students.destroy', $student) }}" onsubmit="return confirm('Nonaktifkan siswa ini? Data transaksi yang sudah tercatat akan tetap disimpan.')" class="deactivate-form">
    @csrf
    @method('DELETE')
    <button class="text-danger" type="submit">Nonaktifkan siswa</button>
</form>
@endsection