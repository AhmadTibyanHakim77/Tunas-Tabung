<!doctype html>
<html lang="id">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="theme-color" content="#f7f8fa">
    <link rel="icon" type="image/svg+xml" href="{{ asset('tunas-tabung-mark.svg') }}">
    <title>Masuk · Tunas Tabung</title>@vite(['resources/css/app.css','resources/js/app.js'])
</head>

<body class="login-page">
    <main class="login-card"><a class="brand login-brand" href="{{ route('login') }}" aria-label="Tunas Tabung"><img class="brand-lockup" src="{{ asset('tunas-tabung-logo.svg') }}" alt=""></a>
        <p class="eyebrow">PORTAL GURU</p>
        <h1>Selamat datang kembali</h1>
        <p class="muted">Masuk untuk mengelola tabungan siswa dengan mudah.</p>@if($errors->any())<div class="notice error">{{ $errors->first() }}</div>@endif<form method="POST" action="{{ route('login.store') }}" class="form-stack">@csrf<label>Email<input type="email" name="email" value="{{ old('email') }}" autocomplete="username" required autofocus></label><label>Kata sandi<input type="password" name="password" autocomplete="current-password" required></label><label class="check"><input type="checkbox" name="remember"> Ingat saya</label><button class="button primary full">Masuk ke dashboard <span>→</span></button></form>
        <p class="login-foot">Akses khusus untuk guru pengelola.</p>
    </main>
</body>

</html>