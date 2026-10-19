# 🌱 Tunas Tabung

**Tunas Tabung** adalah aplikasi manajemen tabungan siswa berbasis web yang dirancang untuk membantu sekolah dalam mengelola data siswa, transaksi tabungan, dan informasi saldo secara lebih terstruktur, efisien, dan mudah digunakan.

Aplikasi ini dikembangkan dengan pendekatan antarmuka modern dan responsif sehingga dapat digunakan oleh pengelola tabungan sekolah dalam melakukan pencatatan serta pemantauan aktivitas tabungan siswa.

---

## ✨ Fitur Utama

### 📊 Dashboard

Dashboard menyediakan ringkasan informasi tabungan secara terpusat.

- Total siswa
- Total saldo tabungan
- Total transaksi
- Ringkasan aktivitas tabungan
- Statistik transaksi
- Grafik perkembangan tabungan
- Informasi transaksi terbaru

Dashboard dirancang agar informasi penting dapat dipahami dengan cepat tanpa harus membuka halaman satu per satu.

### 👨‍🎓 Manajemen Data Siswa

Fitur pengelolaan data siswa digunakan untuk menyimpan dan mengatur informasi siswa.

Fitur meliputi:

- Menampilkan daftar siswa
- Menambahkan siswa
- Mengubah data siswa
- Menghapus data siswa
- Melihat detail siswa
- Melihat saldo tabungan siswa
- Pencarian data siswa
- Pengelompokan berdasarkan kelas

### 💰 Manajemen Transaksi

Sistem transaksi digunakan untuk mencatat seluruh aktivitas tabungan siswa.

Jenis transaksi:

- Setoran tabungan
- Penarikan tabungan

Informasi transaksi mencakup:

- Nama siswa
- Jenis transaksi
- Nominal
- Tanggal transaksi
- Keterangan
- Saldo setelah transaksi

Setiap transaksi tercatat secara terstruktur sehingga riwayat tabungan dapat ditelusuri dengan mudah.

### 📈 Statistik dan Grafik

Tunas Tabung menyediakan visualisasi data untuk membantu pengguna memahami perkembangan tabungan.

Visualisasi dapat digunakan untuk menampilkan:

- Perkembangan saldo
- Jumlah setoran
- Jumlah penarikan
- Aktivitas transaksi
- Statistik tabungan siswa

Grafik membantu pengguna memperoleh informasi tanpa harus membaca seluruh data transaksi secara manual.

### 🔎 Pencarian dan Pengelolaan Data

Sistem menyediakan fitur untuk mempermudah pengelolaan data dalam jumlah banyak.

- Search siswa
- Filter data
- Sorting data
- Pagination
- Detail data
- Riwayat transaksi

---

## 🎨 User Interface

Tunas Tabung menggunakan konsep desain modern yang mengutamakan:

- Clean interface
- Soft 3D visual
- Glassmorphism
- Rounded components
- Subtle shadow
- Smooth animation
- Responsive layout
- Visual hierarchy
- Consistent spacing
- Modern dashboard design

Desain dibuat agar aplikasi tetap terlihat profesional tanpa mengurangi kemudahan penggunaan.

---

## 🛠️ Tech Stack

### Backend

- **PHP**
- **Laravel**
- **Laravel Blade**
- **Laravel Eloquent ORM**

### Frontend

- **HTML5**
- **CSS3**
- **JavaScript**
- **Blade Template**

### Database

- **MySQL**

### Development Tools

- **Composer**
- **Node.js**
- **NPM**
- **Git**
- **GitHub**
- **Visual Studio Code**

---

## 📁 Project Structure

Struktur utama aplikasi Laravel:

```text
TUNAS TABUNG/
├── app/
│   ├── Http/
│   │   ├── Controllers/
│   │   ├── Middleware/
│   │   └── Requests/
│   └── Models/
│
├── bootstrap/
│
├── config/
│
├── database/
│   ├── factories/
│   ├── migrations/
│   └── seeders/
│
├── public/
│
├── resources/
│   ├── css/
│   ├── js/
│   └── views/
│
├── routes/
│   ├── web.php
│   └── api.php
│
├── storage/
│
├── tests/
│
├── .env.example
├── artisan
├── composer.json
├── package.json
└── README.md
