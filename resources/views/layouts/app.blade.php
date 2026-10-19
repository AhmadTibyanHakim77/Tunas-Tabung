<!doctype html>
<html lang="id">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="theme-color" content="#0f172a">
    <link rel="icon" type="image/svg+xml" href="{{ asset('tunas-tabung-mark.svg') }}">
    <link rel="apple-touch-icon" href="{{ asset('tunas-tabung-mark.svg') }}">
    <title>@yield('title', 'Dashboard') · Tunas Tabung</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>

<body>
    <div class="shell">
        <aside class="sidebar">
            <a class="brand" href="{{ route('dashboard') }}" aria-label="Tunas Tabung, ke ringkasan">
                <img class="brand-lockup" src="{{ asset('tunas-tabung-logo-dark.svg') }}" alt="">
            </a>
            <div class="nav-label">MENU UTAMA</div>
            <nav class="nav-links" aria-label="Navigasi utama">
                <a class="{{ request()->routeIs('dashboard') ? 'active' : '' }}" href="{{ route('dashboard') }}" @if(request()->routeIs('dashboard')) aria-current="page" @endif>
                    <span aria-hidden="true"><svg viewBox="0 0 20 20" fill="none">
                            <rect x="3" y="3" width="5" height="5" rx="1" stroke="currentColor" stroke-width="1.5" />
                            <rect x="12" y="3" width="5" height="5" rx="1" stroke="currentColor" stroke-width="1.5" />
                            <rect x="3" y="12" width="5" height="5" rx="1" stroke="currentColor" stroke-width="1.5" />
                            <rect x="12" y="12" width="5" height="5" rx="1" stroke="currentColor" stroke-width="1.5" />
                        </svg></span> Ringkasan
                </a>
                <a class="{{ request()->routeIs('students.*') ? 'active' : '' }}" href="{{ route('students.index') }}" @if(request()->routeIs('students.*')) aria-current="page" @endif>
                    <span aria-hidden="true"><svg viewBox="0 0 20 20" fill="none">
                            <circle cx="10" cy="6" r="3" stroke="currentColor" stroke-width="1.5" />
                            <path d="M4 17v-1.5a6 6 0 0 1 12 0V17H4Z" stroke="currentColor" stroke-width="1.5" stroke-linejoin="round" />
                        </svg></span> Data siswa
                </a>
                <a class="{{ request()->routeIs('transactions.*') ? 'active' : '' }}" href="{{ route('transactions.index') }}" @if(request()->routeIs('transactions.*')) aria-current="page" @endif>
                    <span aria-hidden="true"><svg viewBox="0 0 20 20" fill="none">
                            <path d="M4 6h11m0 0-2.5-2.5M15 6l-2.5 2.5M16 14H5m0 0 2.5-2.5M5 14l2.5 2.5" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" />
                        </svg></span> Transaksi
                </a>
            </nav>
            <div class="sidebar-bottom">
                <div class="access-badge"><span class="access-dot"></span><span><strong>Mode lokal</strong><small>Akses tanpa login</small></span></div>
            </div>
        </aside>
        <main class="main-area">
            <header class="topbar">
                <div>
                    <span class="eyebrow">TUNAS TABUNG / @yield('crumb', 'RINGKASAN')</span>
                    <h1>@yield('heading', 'Ringkasan')</h1>
                </div>
                <div class="today"><span class="today-dot"></span>{{ now()->locale('id')->translatedFormat('l, d F Y') }}</div>
            </header>
            <div class="page-content">
                @if(session('success'))
                <div class="notice success" role="status"><span aria-hidden="true">✓</span>{{ session('success') }}</div>
                @endif
                @if($errors->any())
                <div class="notice error" role="alert"><span aria-hidden="true">!</span>
                    <div>Periksa kembali isian Anda. {{ $errors->first() }}</div>
                </div>
                @endif
                @yield('content')
            </div>
            <footer>© {{ now()->year }} Tunas Tabung <span>Kelola tabungan, tumbuhkan kebiasaan baik.</span></footer>
        </main>
    </div>
</body>

</html>