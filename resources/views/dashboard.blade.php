@extends('layouts.app')
@section('title', 'Dashboard')
@section('heading', 'Ringkasan tabungan')
@section('content')
<section class="welcome-banner">
    <div class="welcome-copy">
        <span class="welcome-kicker"><span></span> PORTAL TABUNGAN SEKOLAH</span>
        <h2>Selamat bertugas, pengelola.</h2>
        <p>Pantau saldo dan aktivitas tabungan siswa dalam satu ringkasan.</p>
    </div>
    <div class="welcome-actions">
        <a class="button button-light" href="{{ route('transactions.create') }}"><span aria-hidden="true"><svg viewBox="0 0 20 20" fill="none">
                    <path d="M10 4v12M4 10h12" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" />
                </svg></span> Catat transaksi</a>
        <a class="welcome-secondary" href="{{ route('students.create') }}">Tambah siswa <span aria-hidden="true">→</span></a>
    </div>
    <div class="coin-stage" aria-hidden="true"><img class="coin-fallback" src="{{ asset('tunas-tabung-mark.svg') }}" alt=""><canvas data-savings-coin></canvas></div>
    <div class="banner-orbit orbit-one"></div>
    <div class="banner-orbit orbit-two"></div>
</section>

<div class="section-heading">
    <div><span class="eyebrow">GAMBARAN UMUM</span>
        <h2>Performa tabungan</h2>
    </div>
    <span class="period-label"><span></span> Ringkasan keseluruhan</span>
</div>
<div class="stats-grid">
    <article class="stat-card">
        <div class="stat-topline"><span class="stat-icon mint" aria-hidden="true"><svg viewBox="0 0 20 20" fill="none">
                    <path d="M3 16.5h14M5.5 13V8m4.5 5V4m4.5 9v-3" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" />
                </svg></span><span class="stat-trend">Terdaftar</span></div>
        <span class="stat-label">Total siswa</span><strong>{{ number_format($studentTotal, 0, ',', '.') }}</strong>
        <small>seluruh data siswa</small>
    </article>
    <article class="stat-card">
        <div class="stat-topline"><span class="stat-icon mint" aria-hidden="true"><svg viewBox="0 0 20 20" fill="none">
                    <circle cx="10" cy="6" r="3" stroke="currentColor" stroke-width="1.5" />
                    <path d="M4 17v-1.5a6 6 0 0 1 12 0V17H4Z" stroke="currentColor" stroke-width="1.5" stroke-linejoin="round" />
                </svg></span><span class="stat-trend">Aktif</span></div>
        <span class="stat-label">Siswa aktif</span><strong>{{ number_format($studentCount, 0, ',', '.') }}</strong>
        <small>siswa yang masih menabung</small>
    </article>
    <article class="stat-card featured">
        <div class="stat-topline"><span class="stat-label">Total saldo tabungan</span><span class="balance-symbol" aria-hidden="true">Rp</span></div>
        <strong>{{ 'Rp '.number_format($balanceTotal, 0, ',', '.') }}</strong>
        <small>saldo bersih seluruh siswa</small>
    </article>
    <article class="stat-card">
        <div class="stat-topline"><span class="stat-icon blue" aria-hidden="true"><svg viewBox="0 0 20 20" fill="none">
                    <path d="M4 14 8 10l2.5 2.5L16 7m0 0h-4m4 0v4" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" />
                </svg></span><span class="stat-trend">@if ($depositTrend !== null){{ $depositTrend > 0 ? '+' : '' }}{{ number_format($depositTrend, 1, ',', '.') }}%@else Bulanan @endif</span></div>
        <span class="stat-label">Total setoran</span><strong>{{ 'Rp '.number_format($depositTotal, 0, ',', '.') }}</strong>
        <small>{{ $depositTrend === null ? 'Belum ada pembanding bulan lalu' : 'Dibanding bulan lalu' }}</small>
    </article>
    <article class="stat-card">
        <div class="stat-topline"><span class="stat-icon peach" aria-hidden="true"><svg viewBox="0 0 20 20" fill="none">
                    <path d="m4 7 4 4 2.5-2.5L16 14m0 0h-4m4 0v-4" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" />
                </svg></span><span class="stat-trend">@if ($withdrawalTrend !== null){{ $withdrawalTrend > 0 ? '+' : '' }}{{ number_format($withdrawalTrend, 1, ',', '.') }}%@else Bulanan @endif</span></div>
        <span class="stat-label">Total penarikan</span><strong>{{ 'Rp '.number_format($withdrawalTotal, 0, ',', '.') }}</strong>
        <small>{{ $withdrawalTrend === null ? 'Belum ada pembanding bulan lalu' : 'Dibanding bulan lalu' }}</small>
    </article>
    <article class="stat-card">
        <div class="stat-topline"><span class="stat-icon blue" aria-hidden="true"><svg viewBox="0 0 20 20" fill="none">
                    <path d="M4 5.5h12v10H4zM7 3.5v4m6-4v4M4 9h12" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" />
                </svg></span><span class="stat-trend">@if ($transactionTrend !== null){{ $transactionTrend > 0 ? '+' : '' }}{{ number_format($transactionTrend, 1, ',', '.') }}%@else Semua waktu @endif</span></div>
        <span class="stat-label">Aktivitas transaksi</span><strong>{{ number_format($transactionCount, 0, ',', '.') }}</strong>
        <small>{{ $transactionTrend === null ? 'transaksi tercatat' : 'Dibanding bulan lalu' }}</small>
    </article>
</div>

<div class="section-heading analytics-heading">
    <div><span class="eyebrow">ANALITIK</span>
        <h2>Perkembangan enam bulan</h2>
    </div>
    <span class="period-label">{{ now()->subMonths(5)->locale('id')->translatedFormat('M Y') }} – {{ now()->locale('id')->translatedFormat('M Y') }}</span>
</div>
<section class="analytics-grid" aria-label="Grafik analitik tabungan enam bulan terakhir">
    <article class="panel chart-panel chart-panel-wide">
        <div class="panel-heading">
            <div>
                <h2>Perkembangan saldo</h2>
                <p>Saldo akhir setiap bulan berdasarkan setoran dan penarikan.</p>
            </div>
        </div>
        @if ($chartData['hasActivity'])
        <div class="chart-surface" aria-busy="true"><span class="chart-loading" role="status">Memuat grafik...</span><canvas data-chart="balance" role="img" aria-label="Grafik perkembangan saldo tabungan selama enam bulan"></canvas></div>
        @else
        <div class="chart-empty"><strong>Belum ada data saldo</strong>
            <p>Grafik akan terisi setelah transaksi pertama dicatat.</p>
        </div>
        @endif
    </article>
    <article class="panel chart-panel">
        <div class="panel-heading">
            <div>
                <h2>Setoran dan penarikan</h2>
                <p>Perbandingan nominal tiap bulan.</p>
            </div>
        </div>
        @if ($chartData['hasActivity'])
        <div class="chart-surface" aria-busy="true"><span class="chart-loading" role="status">Memuat grafik...</span><canvas data-chart="cashflow" role="img" aria-label="Grafik perbandingan setoran dan penarikan bulanan"></canvas></div>
        @else
        <div class="chart-empty"><strong>Belum ada aktivitas</strong>
            <p>Data setoran dan penarikan akan tampil di sini.</p>
        </div>
        @endif
    </article>
    <article class="panel chart-panel">
        <div class="panel-heading">
            <div>
                <h2>Aktivitas transaksi</h2>
                <p>Jumlah transaksi yang dicatat setiap bulan.</p>
            </div>
        </div>
        @if ($chartData['hasActivity'])
        <div class="chart-surface" aria-busy="true"><span class="chart-loading" role="status">Memuat grafik...</span><canvas data-chart="activity" role="img" aria-label="Grafik jumlah transaksi bulanan"></canvas></div>
        @else
        <div class="chart-empty"><strong>Belum ada aktivitas</strong>
            <p>Jumlah transaksi bulanan akan tampil di sini.</p>
        </div>
        @endif
    </article>
    <article class="panel chart-panel">
        <div class="panel-heading">
            <div>
                <h2>Komposisi transaksi</h2>
                <p>Perbandingan jumlah setoran dan penarikan.</p>
            </div>
        </div>
        @if ($chartData['hasActivity'])
        <div class="chart-surface" aria-busy="true"><span class="chart-loading" role="status">Memuat grafik...</span><canvas data-chart="distribution" role="img" aria-label="Distribusi jumlah setoran dan penarikan"></canvas></div>
        @else
        <div class="chart-empty"><strong>Belum ada aktivitas</strong>
            <p>Distribusi transaksi akan tampil setelah transaksi pertama.</p>
        </div>
        @endif
    </article>
</section>

<section class="panel top-savers-panel" aria-label="Siswa dengan saldo tertinggi">
    <div class="panel-heading">
        <div>
            <h2>Tabungan siswa teratas</h2>
            <p>Lima saldo tertinggi dari transaksi yang tercatat.</p>
        </div>
        <a class="text-link" href="{{ route('students.index') }}">Semua siswa <span aria-hidden="true">→</span></a>
    </div>
    @if ($topStudents->isEmpty())
    <div class="chart-empty"><strong>Belum ada tabungan siswa</strong>
        <p>Data peringkat akan muncul setelah siswa melakukan setoran.</p>
    </div>
    @else
    <ol class="top-savers-list">
        @foreach ($topStudents as $topStudent)
        @php($progress = $topStudentBalance > 0 ? (int) round(($topStudent->balance / $topStudentBalance) * 100) : 0)
        <li class="top-saver-row">
            <span class="saver-rank">{{ str_pad((string) $loop->iteration, 2, '0', STR_PAD_LEFT) }}</span>
            <span class="mini-avatar" aria-hidden="true">{{ strtoupper(substr($topStudent->name, 0, 1)) }}</span>
            <div class="saver-profile">
                <strong>{{ $topStudent->name }}</strong>
                <small>{{ $topStudent->student_number }} · Kelas {{ $topStudent->class }}</small>
                <progress class="saver-progress" aria-label="Perbandingan saldo {{ $topStudent->name }}" value="{{ $progress }}" max="100">{{ $progress }}%</progress>
            </div>
            <div class="saver-balance"><strong>Rp {{ number_format((int) $topStudent->balance, 0, ',', '.') }}</strong><small>{{ number_format((int) $topStudent->transaction_count, 0, ',', '.') }} transaksi</small></div>
        </li>
        @endforeach
    </ol>
    @endif
</section>

<section class="panel activity-panel">
    <div class="panel-heading">
        <div class="panel-title-wrap"><span class="panel-icon" aria-hidden="true"><svg viewBox="0 0 20 20" fill="none">
                    <path d="M4 6h11m0 0-2.5-2.5M15 6l-2.5 2.5M16 14H5m0 0 2.5-2.5M5 14l2.5 2.5" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" />
                </svg></span>
            <div>
                <h2>Transaksi terbaru</h2>
                <p>Setoran dan penarikan yang baru dicatat.</p>
            </div>
        </div>
        <a class="text-link" href="{{ route('transactions.index') }}">Lihat semua transaksi <span aria-hidden="true">→</span></a>
    </div>
    <div class="table-wrap">
        <table>
            <thead>
                <tr>
                    <th>SISWA</th>
                    <th>TANGGAL</th>
                    <th>JENIS</th>
                    <th>GURU</th>
                    <th class="right">NOMINAL</th>
                </tr>
            </thead>
            <tbody>
                @forelse($recentTransactions as $transaction)
                <tr>
                    <td><a class="student-link" href="{{ route('students.show', $transaction->student) }}"><span class="mini-avatar">{{ strtoupper(substr($transaction->student->name, 0, 1)) }}</span><span><strong>{{ $transaction->student->name }}</strong><small>{{ $transaction->student->student_number }} · Kelas {{ $transaction->student->class }}</small></span></a></td>
                    <td>{{ $transaction->transaction_date->format('d M Y') }}</td>
                    <td><span class="pill {{ $transaction->type }}">{{ $transaction->type === 'deposit' ? 'Setoran' : 'Penarikan' }}</span></td>
                    <td>{{ $transaction->user?->name ?? "Pengelola" }}</td>
                    <td class="right amount {{ $transaction->type }}">{{ $transaction->type === 'deposit' ? '+' : '−' }} Rp {{ number_format($transaction->amount, 0, ',', '.') }}</td>
                </tr>
                @empty
                <tr>
                    <td colspan="5">
                        <div class="empty-state"><span class="empty-illustration" aria-hidden="true">＋</span><strong>Belum ada transaksi</strong>
                            <p>Catat setoran pertama untuk mulai melihat aktivitas tabungan siswa.</p><a class="text-link" href="{{ route('transactions.create') }}">Catat transaksi pertama →</a>
                        </div>
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
    <div class="activity-footer"><span>{{ number_format($transactionCount, 0, ',', '.') }} transaksi tersimpan</span><a href="{{ route('students.index') }}">Kelola data siswa <span aria-hidden="true">→</span></a></div>
</section>
<script type="application/json" id="dashboard-chart-data">
    @json($chartData)
</script>
@endsection