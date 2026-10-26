# GarudaHub — Refactoring PHP Native ke Laravel

Proyek praktikum **KST1407 Pemrograman Berbasis Web**. GarudaHub adalah aplikasi lelang proyek IT yang mempertemukan klien dan developer. Proyek ini dibuat dengan merancang ulang aplikasi PHP Native menjadi aplikasi **Laravel berbasis MVC** dan menggunakan **Eloquent ORM** untuk mengelola database.

- Pengembang: Diego Valentino Augusta
- Program Studi: Teknologi Informasi, Universitas Jember
- Referensi studi kasus: [garudaHub PHP Native oleh diksy](https://github.com/diksy/garudaHub)

## Fitur yang diimplementasikan

- Registrasi, login, dan logout dengan peran **klien** serta **developer**.
- Klien dapat menambahkan, melihat, mengubah, dan menghapus proyek miliknya.
- Pengguna yang login dapat melihat daftar serta detail proyek.
- Developer dapat mengirim penawaran berupa harga, durasi, dan proposal.
- Setiap developer hanya dapat mengirim satu penawaran untuk proyek yang sama.
- Klien dapat melihat penawaran yang masuk dan memilih pemenang.
- Setelah pemenang dipilih, status proyek otomatis berubah menjadi **ditutup**.
- Validasi formulir, pembatasan akses berdasarkan peran, CSRF protection, dan relasi database.

**Catatan ruang lingkup:** Referensi PHP Native juga memiliki fitur pencarian proyek, dashboard per peran, dan panel admin. Fitur-fitur tersebut **belum direplikasi sepenuhnya** pada versi Laravel ini.

## Teknologi

- PHP 8.3 atau lebih baru
- Laravel 13
- MySQL / MariaDB
- Eloquent ORM, Blade, dan Laravel Migration
- Composer, Git, dan GitHub
- Laragon untuk menjalankan aplikasi secara lokal (opsional)

## Struktur MVC

| Bagian | Lokasi | Fungsi |
| --- | --- | --- |
| Model | `app/Models/User.php`, `Project.php`, `Bid.php` | Relasi dan pengolahan data menggunakan Eloquent |
| Controller | `app/Http/Controllers/` | Proses login, CRUD proyek, penawaran, dan pemilihan pemenang |
| View | `resources/views/` | Tampilan halaman menggunakan Blade |
| Routes | `routes/web.php` | Pemetaan URL ke controller |
| Migration | `database/migrations/` | Membuat struktur dan relasi tabel database |

## Rancangan database

Tiga tabel utama yang digunakan:

1. **users**: menyimpan data akun, termasuk nama, email, password (hash), dan role.
2. **projects**: menyimpan informasi proyek dan `client_id` sebagai relasi ke `users.id`.
3. **bids**: menyimpan penawaran dengan `project_id` yang mengarah ke `projects.id` dan `developer_id` yang mengarah ke `users.id`.

Relasi Eloquent:

- `User hasMany Project`, `Project belongsTo User` (klien).
- `Project hasMany Bid`, `Bid belongsTo Project`.
- `User hasMany Bid`, `Bid belongsTo User` (developer).

Migration menerapkan *foreign key*, penghapusan berantai (*cascade delete*), dan aturan unik `project_id + developer_id` sehingga satu developer tidak dapat menawar dua kali pada proyek yang sama.

## Menjalankan aplikasi secara lokal

Pastikan PHP, Composer, dan MySQL/MariaDB aktif. Pada contoh ini terminal menggunakan PowerShell di Windows.

1. Ambil source dan masuk ke folder proyek:

   ```powershell
   git clone https://github.com/DiegoPrac/garuda-hub-laravel.git
   cd garuda-hub-laravel
   composer install
   ```

2. Buat file konfigurasi lokal:

   ```powershell
   Copy-Item .env.example .env
   php artisan key:generate
   ```

3. Buat database kosong melalui phpMyAdmin atau aplikasi database lokal. Kemudian ubah bagian koneksi pada `.env` sesuai pengaturan MySQL, contohnya:

   ```dotenv
   DB_CONNECTION=mysql
   DB_HOST=127.0.0.1
   DB_PORT=3306
   DB_DATABASE=garuda-hub-laravel
   DB_USERNAME=root
   DB_PASSWORD=
   ```

   Contoh tersebut untuk **lingkungan lokal** dengan akun MySQL tanpa password; sesuaikan dengan sistem masing-masing.

4. Jalankan migration:

   ```powershell
   php artisan config:clear
   php artisan migrate
   ```

5. Jalankan server pengembangan:

   ```powershell
   php artisan serve
   ```

   Buka URL yang ditampilkan terminal. Jika menggunakan Laragon dengan virtual host, proyek juga dapat diakses melalui alamat `.test` yang dikonfigurasi Laragon.

6. Registrasikan akun klien dan developer melalui aplikasi untuk mencoba alur lelang. Database dari source PHP Native **tidak harus diimpor**, karena tabel aplikasi Laravel dibuat melalui migration.

## Alur pengujian manual

1. Daftar dan masuk sebagai **klien**, lalu buat proyek baru.
2. Pastikan proyek muncul dalam daftar dan dapat dilihat detailnya.
3. Uji fitur edit proyek.
4. Keluar, kemudian masuk sebagai **developer**.
5. Buka detail proyek, kirim penawaran harga, durasi, dan proposal.
6. Masuk kembali sebagai klien pemilik proyek, lihat penawaran yang masuk, kemudian pilih pemenang.
7. Periksa bahwa penawaran pemenang bertanda **Terpilih** dan status proyek berubah menjadi **Ditutup**.
8. Untuk memeriksa database, buka tabel `users`, `projects`, dan `bids` melalui phpMyAdmin.

Pemeriksaan tambahan:

```powershell
php artisan route:list
php artisan migrate:status
php artisan test
```

Perintah tes terakhir disediakan untuk pemeriksaan lokal; jangan menganggap semua pengujian otomatis berhasil sebelum benar-benar dijalankan.

## Catatan keamanan dan pengumpulan

- File `.env` berisi konfigurasi rahasia dan **tidak boleh diunggah** ke GitHub.
- Aplikasi ini digunakan untuk tugas dan demonstrasi **di komputer lokal**, belum dikonfigurasi untuk server produksi.
- Riwayat pengembangan dicatat menggunakan Git melalui beberapa commit terpisah.
- Untuk mengumpulkan tugas, gunakan URL repository ini, **bukan** URL `localhost` atau `.test`.

## Referensi

- [Source PHP Native — diksy/garudaHub](https://github.com/diksy/garudaHub)
- [Dokumentasi Laravel](https://laravel.com/docs)
