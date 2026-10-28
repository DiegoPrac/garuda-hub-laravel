# Episode 2: The Phantom Vulnerability
## Security Audit — GarudaHub

**Mata Kuliah:** Pemrograman Berbasis Web  
**Kelompok:** 5  
**Aplikasi:** GarudaHub (Laravel)  
**Tools:** OWASP ZAP 2.17.0, PHP 8.3.36, Laravel  
**Tanggal Pengujian:** 28 Oktober 2026

## 1. Tujuan Pengujian

Melakukan audit keamanan pada aplikasi GarudaHub,
mengidentifikasi potensi kerentanan, menerapkan perbaikan,
dan membandingkan hasil pengujian sebelum dan sesudah
perbaikan.

## 2. Metode Pengujian

Pengujian dilakukan pada server lokal menggunakan
database khusus `garudahub_zap`.

Tahapan pengujian:
1. Manual Explore dan Passive Scan OWASP ZAP.
2. Spider Scan untuk menemukan endpoint aplikasi.
3. Active Scan menggunakan policy Dev Standard.
4. Analisis temuan keamanan.
5. Implementasi perbaikan dan pengujian ulang.
6. Automated Testing Laravel.

Pemindaian ZAP terutama dilakukan tanpa autentikasi,
sehingga hasilnya tidak mencakup seluruh fungsi
yang hanya tersedia setelah login.

## 3. Hasil Sebelum dan Sesudah Perbaikan

| Tingkat Risiko | Before Fix | After Fix |
|---|---:|---:|
| High | 0 | 0 |
| Medium | 2 | 1 |
| Low | 3 | 3 |
| Informational | 2 | 2 |
| Total kategori alert | 7 | 6 |

Jumlah kategori alert berkurang dari 7 menjadi 6.
Temuan dengan risiko Medium berkurang dari 2 menjadi 1.

## 4. Perbaikan yang Dilakukan

### 4.1 Security Headers

Membuat middleware `SecurityHeaders.php` yang
menambahkan header:

- Content-Security-Policy
- X-Frame-Options: DENY
- X-Content-Type-Options: nosniff
- Referrer-Policy: strict-origin-when-cross-origin

Middleware didaftarkan melalui `bootstrap/app.php`.

### 4.2 Content Security Policy (CSP)

Mengaktifkan CSP untuk membatasi sumber konten
yang dapat dimuat oleh browser.

JavaScript inline pada konfirmasi hapus proyek dan
pilih pemenang dipindahkan ke file eksternal
`public/js/confirm-actions.js`.

### 4.3 PHP

Menggunakan PHP 8.3.36 dan menonaktifkan
`expose_php` pada lingkungan pengujian.

### 4.4 Laravel Boost

Menonaktifkan browser logger pada lingkungan lokal
agar tidak menyisipkan JavaScript inline yang
melanggar kebijakan CSP.

## 5. Verifikasi Perbaikan

Pengujian manual meliputi:
- Login dan logout Klien.
- Login Developer.
- Halaman daftar proyek.
- Halaman detail proyek.
- Halaman tambah proyek.
- Tampilan CSS.
- Konfirmasi penghapusan dengan tombol Cancel.

Pengujian otomatis Laravel menghasilkan:

**32 tests passed, 167 assertions.**

Hasil automated testing tetap lulus setelah
perbaikan security headers dan CSP.

## 6. Temuan yang Masih Tersisa

### CSP Header Not Set

Masih terdeteksi pada `/css` dan `/js`.
Kedua endpoint tersebut menghasilkan HTTP 404
dari server pengembangan, bukan halaman utama aplikasi.

### X-Content-Type-Options Header Missing

Masih terdeteksi pada file CSS dan JavaScript statis
yang disajikan langsung oleh server pengembangan.

### Cookie No HttpOnly Flag

Terdeteksi pada cookie `XSRF-TOKEN`.
Temuan ini memerlukan evaluasi berdasarkan
mekanisme CSRF Laravel dan tidak otomatis
menunjukkan kerentanan sesi autentikasi.

### Big Redirect Detected

Memerlukan pemeriksaan lebih lanjut untuk memastikan
apakah redirect mengandung informasi sensitif.

Alert Authentication Request Identified dan
Session Management Response Identified bersifat
informasional.

## 7. Kesimpulan

Perbaikan keamanan GarudaHub berhasil diterapkan pada
halaman aplikasi yang diuji, terutama untuk proteksi
clickjacking dan penerapan Content Security Policy.

Jumlah kategori temuan OWASP ZAP berkurang dari
7 menjadi 6. Seluruh 32 automated tests Laravel tetap lulus.

Temuan yang tersisa telah didokumentasikan untuk
evaluasi lanjutan. Hasil pemindaian otomatis ini tidak
menjamin bahwa aplikasi bebas dari seluruh kerentanan.


## 8. Bukti Pengujian

Berikut dokumentasi hasil pengujian keamanan aplikasi
GarudaHub menggunakan OWASP ZAP 2.17.0.

### Screenshot Bukti Pengujian

[Screenshot OWASP ZAP After Fix - Kelompok 5 - NIM 252410102108](https://drive.google.com/file/d/19j_TzZQTO9UpN_Jki1Wo_5F1eD8A-XuK/view?usp=sharing)

Screenshot menampilkan hasil pemindaian OWASP ZAP
dengan 0 temuan High, serta komentar identitas dan NIM
pada kode aplikasi yang ditampilkan berdampingan.

### Laporan Hasil Scanning

- [Laporan OWASP ZAP - Before Fix (Baseline)](https://drive.google.com/file/d/1wksR5L7jp0bAIXtB6MghYtoMa_PIks56/view?usp=sharing)
- [Laporan OWASP ZAP - After Fix](https://drive.google.com/file/d/1Wp512Z3DGqaITe3NMI_77jeVrIzgLKmV/view?usp=sharing)

Laporan berisi hasil identifikasi kerentanan,
tingkat risiko, dan perbandingan sebelum serta
sesudah implementasi perbaikan keamanan.
