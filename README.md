# Tunas Tabung

Aplikasi tabungan siswa sekolah dasar yang dikelola guru. Guru dapat mengelola data siswa, mencatat setoran dan penarikan, serta melihat saldo dan riwayat transaksi.

## Fitur

- Akses lokal tanpa login untuk dashboard, siswa, dan transaksi.
- Dashboard ringkasan jumlah siswa, setoran, penarikan, saldo, dan transaksi terbaru.
- CRUD siswa, pencarian, filter kelas, dan penonaktifan siswa.
- Pencatatan setoran dan penarikan dengan validasi saldo.
- Riwayat transaksi dan detail tabungan siswa.
- Saldo dihitung dari transaksi setoran dikurangi penarikan.

## Teknologi

- PHP 8.3+ dan Laravel 13
- Blade, Tailwind CSS 4, Vite
- SQLite secara default; MySQL/MariaDB dan PostgreSQL tersedia melalui konfigurasi Laravel
- PHPUnit

## Menjalankan aplikasi

1. Pasang dependency PHP dan frontend:

    ```sh
    composer install
    npm install
    ```

2. Siapkan environment dan database:

    ```sh
    cp .env.example .env
    php artisan key:generate
    touch database/database.sqlite
    php artisan migrate --seed
    ```

    Seeder development membuat 8 siswa fiktif dan contoh transaksi. Atur koneksi dan kredensial database melalui `.env` bila menggunakan database selain SQLite. Akses aplikasi saat ini tidak memerlukan akun.

3. Bangun aset dan jalankan server:

    ```sh
    npm run build
    php artisan serve
    ```

    Untuk pengembangan frontend, jalankan `npm run dev` pada terminal terpisah.

## Tes

```sh
php artisan test
```

Tes menggunakan SQLite in-memory dan mencakup akses tanpa login, data siswa, transaksi setoran/penarikan, saldo tidak mencukupi, serta tampilan detail tabungan.

## Struktur utama

- `app/Models` — pengguna, siswa, dan transaksi tabungan.
- `app/Http/Controllers` — autentikasi, dashboard, siswa, dan transaksi.
- `database/migrations` — skema pengguna, siswa, dan transaksi.
- `resources/views` — antarmuka Blade.
- `routes/web.php` — rute web dan akses guru.

# TUNAS TABUNG - AGENT RULES

## Project

Laravel application for managing student savings.

## Working rules

- Work directly in the current workspace.
- Inspect existing implementation before changing it.
- Reuse existing components, routes, models, and styles.
- Do not rewrite working code unnecessarily.
- Never create duplicate functionality.
- Never claim a task is complete without verifying it.
- Run relevant tests after changes.
- Fix errors found during verification.
- Keep the implementation production-ready.
- Use Indonesian for UI text.
- Keep UI consistent across all pages.
- Use accessible HTML and responsive layouts.
- Follow existing Laravel conventions.

## Autonomy

- Execute the requested task directly.
- Do not provide a long implementation plan unless requested.
- Do not stop after analysis.
- Do not ask for confirmation for ordinary file edits or terminal commands.
- If an implementation error occurs, diagnose and fix it.
- Continue until the requested feature is actually working.

## Quality

Before finishing:

1. Check changed files.
2. Check routes.
3. Check PHP syntax where applicable.
4. Run relevant Laravel tests.
5. Run frontend checks where applicable.
6. Fix discovered errors.
7. Verify the final result.
