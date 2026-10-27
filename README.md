# GarudaHub - Platform Lelang Proyek IT

GarudaHub merupakan aplikasi berbasis web yang dikembangkan menggunakan Laravel untuk mempertemukan klien yang membutuhkan jasa pengembangan teknologi informasi dengan developer yang menawarkan keahlian mereka.

Melalui GarudaHub, klien dapat membuat dan mengelola proyek, sedangkan developer dapat mengajukan penawaran berdasarkan harga, durasi pengerjaan, dan proposal. Klien kemudian dapat memilih developer sebagai pemenang lelang.

Proyek ini dikembangkan sebagai bagian dari pembelajaran mata kuliah Pemrograman Berbasis Web (KSTI407).

## Identitas Kelompok 5

**Mata Kuliah:** Pemrograman Berbasis Web (KSTI407)  
**Program Studi:** Teknologi Informasi  
**Fakultas:** Ilmu Komputer  
**Universitas:** Universitas Jember

### Daftar Anggota Kelompok

| No. | Nama Lengkap | NIM | Keterangan |
|---|---|---|---|
| 1 | Diego Valentino Augusta | 252410102108 | Pengembang Utama |
| 2 | Fajri Maulana | 252410102036 | Anggota Kelompok |
| 3 | Achmad Mahrusiy Mahfudz | 252410102092 | Anggota Kelompok |

### Keterangan Kontribusi

Proyek GarudaHub dikembangkan secara mandiri oleh **Diego Valentino Augusta** sebagai pengembang utama sekaligus penanggung jawab implementasi teknis aplikasi.

Pengerjaan mencakup perancangan ulang database relasional, migrasi sistem PHP Native ke Laravel dengan arsitektur MVC, implementasi Eloquent ORM, fitur autentikasi pengguna, manajemen proyek, sistem bidding, pemilihan pemenang lelang, pengujian otomatis, peningkatan keamanan, pengembangan antarmuka responsif, serta dokumentasi aplikasi.

Fajri Maulana dan Achmad Mahrusiy Mahfudz tercantum sebagai anggota Kelompok 5 untuk keperluan administrasi kelompok. Pencantuman anggota tidak dimaksudkan untuk menyatakan bahwa seluruh anggota memiliki kontribusi implementasi teknis yang sama.

Seluruh proses pengembangan terdokumentasi melalui riwayat commit Git yang dapat diperiksa pada repository ini.

## Fitur Utama

### 1. Autentikasi Pengguna

- Registrasi akun sebagai klien atau developer.
- Login dan logout pengguna.
- Password disimpan menggunakan hashing.
- Pembatasan percobaan login.
- Hak akses berdasarkan peran pengguna.

### 2. Manajemen Proyek

- Klien dapat membuat proyek baru.
- Klien dapat mengubah informasi proyek miliknya.
- Klien dapat menghapus proyek miliknya.
- Pengguna dapat melihat daftar dan detail proyek.
- Informasi proyek meliputi judul, kategori, deskripsi, budget, batas lelang, dan status.

### 3. Sistem Bidding

- Developer dapat mengirim penawaran proyek.
- Penawaran berisi harga, durasi pengerjaan, dan proposal.
- Satu developer hanya dapat mengirim satu penawaran pada setiap proyek.
- Penawaran hanya diterima selama proyek masih terbuka dan batas lelang belum berakhir.
- Developer dapat melihat ringkasan dan status penawarannya.

### 4. Pemilihan Pemenang

- Pemilik proyek dapat melihat daftar penawaran yang masuk.
- Pemilik proyek dapat memilih penawaran sebagai pemenang.
- Satu proyek hanya dapat memiliki satu pemenang melalui alur aplikasi.
- Status proyek otomatis berubah menjadi ditutup setelah pemenang dipilih.
- Proyek yang sudah ditutup tidak menerima penawaran baru.

### 5. Keamanan Aplikasi

- Pembatasan akses menggunakan Laravel Policy.
- Validasi data pada sisi server.
- Perlindungan CSRF pada formulir.
- Pembatasan akses berdasarkan kepemilikan proyek.
- Transaksi database pada proses bidding dan pemilihan pemenang.

### 6. Antarmuka Responsif

- Tampilan halaman utama yang lebih modern.
- Navigasi sesuai peran pengguna.
- Dashboard klien dan developer.
- Halaman daftar proyek dan detail proyek.
- Formulir dengan tampilan responsif.

## Teknologi yang Digunakan

| Teknologi | Kegunaan |
|---|---|
| PHP | Bahasa pemrograman backend |
| Laravel 13 | Framework aplikasi web |
| MySQL | Database utama aplikasi |
| Eloquent ORM | Pengelolaan dan relasi database |
| Blade | Template antarmuka Laravel |
| HTML dan CSS | Struktur dan desain halaman |
| Git dan GitHub | Version control |
| Laragon | Lingkungan pengembangan lokal |
| PHPUnit | Pengujian otomatis |

## Struktur Proyek

```text
app/
  Http/Controllers/
    AuthController.php
    ProjectController.php
    BidController.php
  Models/
  Policies/
    ProjectPolicy.php

database/
  factories/
  migrations/

public/
  css/
    garudahub.css

resources/
  views/
    auth/
    projects/
    layout.blade.php
    welcome.blade.php

routes/
  web.php

tests/
  Feature/
    GarudaHubFlowTest.php
    GarudaHubSecurityTest.php
    GarudaHubRegressionTest.php
    GarudaHubUiTest.php
```

## Cara Instalasi

### 1. Persiapan

Pastikan komputer sudah memiliki:

- PHP dan Composer
- MySQL
- Git
- Laragon atau web server lokal lainnya

### 2. Clone Repository

```bash
git clone https://github.com/DiegoPrac/garuda-hub-laravel.git
cd garuda-hub-laravel
```

### 3. Instal Dependensi

```bash
composer install
```

### 4. Konfigurasi Environment

Salin file `.env.example` menjadi `.env`.

Untuk pengguna PowerShell:

```powershell
Copy-Item .env.example .env
```

Sesuaikan pengaturan database di file `.env`:

```env
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=garuda-hub-laravel
DB_USERNAME=root
DB_PASSWORD=
```

Pengaturan username, password, dan nama database harus disesuaikan dengan MySQL masing-masing.

Buat database terlebih dahulu, lalu jalankan:

```bash
php artisan key:generate
php artisan migrate
```

Perintah tersebut ditujukan untuk instalasi baru.

### 5. Menjalankan Aplikasi

```bash
php artisan serve
```

Buka melalui browser:

http://127.0.0.1:8000

Jika menggunakan konfigurasi otomatis Laragon, aplikasi juga dapat diakses melalui domain lokal yang sesuai.

## Alur Penggunaan

1. Pengguna melakukan registrasi atau login.
2. Klien membuat proyek beserta detail kebutuhannya.
3. Developer melihat proyek yang tersedia.
4. Developer mengirim penawaran harga, durasi, dan proposal.
5. Klien melihat penawaran yang masuk.
6. Klien memilih developer sebagai pemenang.
7. Status proyek berubah menjadi ditutup.
8. Developer dapat melihat hasil pemilihan pada detail proyek.

## Pengujian Aplikasi

Pengujian dilakukan menggunakan Laravel Artisan dan PHPUnit.

Jalankan:

```bash
php artisan test
```

Hasil pengujian terakhir:

| Pengujian | Hasil |
|---|---|
| Total tests | 32 passed |
| Total assertions | 167 |
| Failed tests | 0 |

Pengujian mencakup alur pengguna, keamanan, hak akses, bidding, pemilihan pemenang, dan tampilan aplikasi.

Pengujian otomatis menggunakan database SQLite untuk lingkungan testing. Pengujian konkurensi khusus MySQL belum dilakukan.

## Version Control

Pengembangan GarudaHub menggunakan Git dan GitHub sebagai sistem version control.

Proses pengembangan dilakukan melalui beberapa commit yang mencatat tahapan implementasi aplikasi, antara lain:

- Perancangan struktur database relasional.
- Implementasi model dan relasi menggunakan Eloquent ORM.
- Implementasi autentikasi dan pembagian peran pengguna.
- Implementasi CRUD proyek.
- Implementasi sistem bidding.
- Implementasi pemilihan pemenang lelang.
- Perbaikan keamanan dan validasi aplikasi.
- Penambahan pengujian otomatis.
- Pengembangan dan penyempurnaan antarmuka pengguna.
- Penyusunan dokumentasi proyek.

Riwayat perubahan dapat diperiksa pada halaman commit GitHub.

[Riwayat Commit GarudaHub](https://github.com/DiegoPrac/garuda-hub-laravel/commits/main/)

## Catatan

- Repository ini berisi source code aplikasi Laravel.
- Aplikasi memerlukan PHP dan database untuk dijalankan.
- File `.env` tidak disertakan karena berisi konfigurasi lokal.
- Aplikasi saat ini dikembangkan dan diuji pada lingkungan lokal.
- Pengembangan teknis aplikasi dilakukan secara mandiri oleh Diego Valentino Augusta.
- Dokumentasi dan riwayat commit tersedia untuk menunjukkan proses pengerjaan.

## Informasi Pengembang

**Diego Valentino Augusta**  
NIM: 252410102108  
Pengembang Utama GarudaHub

Program Studi Teknologi Informasi  
Fakultas Ilmu Komputer  
Universitas Jember

GitHub: [DiegoPrac](https://github.com/DiegoPrac)

## Repository

https://github.com/DiegoPrac/garuda-hub-laravel


