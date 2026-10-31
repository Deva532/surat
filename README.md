# SISTEM INFORMASI PERSURATAN KECAMATAN LAMONGAN (SIPERSURAT)

Aplikasi administrasi persuratan dan disposisi kedinasan modern untuk **Kantor Kecamatan Lamongan, Kabupaten Lamongan**. Sistem ini dibangun menggunakan **Laravel 12**, **Filament PHP v3**, dan **Livewire 3** dengan standar naskah dinas pemerintahan.

Aplikasi terdiri dari 2 modul utama:
1. **Portal Publik (`/`)**: Formulir pengiriman surat online bagi masyarakat, instansi eksternal, dan desa/kelurahan serta pelacakan surat (`/lacak-surat`).
2. **Panel Admin & Pejabat Kedinasan (`/admin`)**: Dashboard persuratan untuk Administrator dan 7 Jabatan Struktural untuk memproses surat masuk, mencetak Lembar Disposisi resmi (standar A4), mengelola surat keluar, dan memantau tindak lanjut.

---

## DAFTAR ISI

1. [Kebutuhan Sistem](#1-kebutuhan-sistem)
2. [Panduan Instalasi (Dari Clone Sampai Running)](#2-panduan-instalasi-dari-clone-sampai-running)
3. [Konfigurasi Web Server & Document Root](#3-konfigurasi-web-server--document-root)
4. [Akun Login Bawaan (Default Credentials)](#4-akun-login-bawaan-default-credentials)
5. [Pemecahan Masalah (Troubleshooting)](#5-pemecahan-masalah-troubleshooting)
   - [Livewire & Aset Filament Bermasalah](#masalah-livewire--aset-filament-tidak-termuat)
   - [Mendiagnosa Kendala Login /admin/login](#mendiagnosa-kendala-halaman-login-adminlogin)
   - [Solusi Unduh Berkas (Mencegah 403 Forbidden)](#solusi-unduh-berkas-lampiran-mencegah-403-forbidden)
   - [Pengisian Surat Fleksibel (Tanpa Paksaan Bintang Merah)](#pengisian-surat-fleksibel)
6. [Perintah Pemeliharaan (Maintenance)](#6-perintah-pemeliharaan-maintenance)

---

## 1. KEBUTUHAN SISTEM

Pastikan environment server atau komputer lokal Anda telah memenuhi prasyarat berikut:

- **PHP**: Versi **8.2** atau **8.3**
- **Ekstensi PHP Wajib**:
  - `BCMath`
  - `Ctype`
  - `Fileinfo`
  - `JSON`
  - `Mbstring`
  - `OpenSSL`
  - `PDO` & `pdo_mysql`
  - `Tokenizer`
  - `XML`
  - `cURL`
  - `GD` atau `Imagick` (untuk pengolahan logo & gambar)
  - `Intl`
- **Database**: MySQL 8.0+ atau MariaDB 10.4+
- **Composer**: Versi 2.x
- **Web Server**: Apache (dengan modul `mod_rewrite` aktif) atau Nginx
- **Lingkungan Lokal yang Disarankan**: Laragon (Windows), XAMPP, atau Laravel Herd

---

## 2. PANDUAN INSTALASI (DARI CLONE SAMPAI RUNNING)

Ikuti langkah-langkah berikut secara berurutan:

### Langkah 1: Clone Repository
Buka terminal / PowerShell dan clone repositori ke direktori web server Anda (contoh di Laragon: `C:\laragon\www` atau `E:\laragon\www`):
```bash
git clone <url-repository-anda> surat
cd surat
```

### Langkah 2: Install Dependensi PHP (Composer)
Jalankan composer install untuk mengunduh semua vendor package:
```bash
composer install
```

### Langkah 3: Siapkan File Environment (`.env`)
Salin file template `.env.example` menjadi `.env`:
- Di Windows (PowerShell):
  ```powershell
  copy .env.example .env
  ```
- Di Linux / macOS:
  ```bash
  cp .env.example .env
  ```

Buka file `.env` dengan text editor dan sesuaikan konfigurasi URL dan Database:
```env
APP_NAME="SIPERSURAT Kecamatan Lamongan"
APP_ENV=local
APP_KEY=
APP_DEBUG=true
APP_TIMEZONE="Asia/Jakarta"
APP_URL=http://surat.test

DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=surat
DB_USERNAME=root
DB_PASSWORD=
```
> **Catatan**: Buat database kosong bernama `surat` pada MySQL/phpMyAdmin sebelum lanjut ke langkah migrasi.

### Langkah 4: Generate Application Key
Generate encryption key unik untuk aplikasi:
```bash
php artisan key:generate
```

### Langkah 5: Jalankan Migrasi Database
Jalankan migration untuk membuat tabel-tabel persuratan, disposisi, dan pengguna:
```bash
php artisan migrate
```

### Langkah 6: Jalankan Seeder Akun Pejabat & Data Master
Jalankan seeder untuk mengisi akun **Superadmin** dan **7 Pejabat Struktural Kecamatan Lamongan**:
```bash
php artisan db:seed --class=JabatanUserSeeder
```

### Langkah 7: Hubungkan Symbolic Link Storage
Pastikan folder `storage/app/public` terhubung ke direktori `public/storage` agar lampiran surat dan logo dinas dapat diakses:
```bash
php artisan storage:link
```

### Langkah 8: Publish & Pastikan Aset Livewire & Filament Siap
Agar UI Filament 3 dan Livewire 3 berjalan lancar tanpa kendala script atau styling:
```bash
php artisan vendor:publish --force --tag=livewire:assets
php artisan filament:assets
php artisan optimize:clear
```

### Langkah 9: Jalankan Aplikasi
- **Jika menggunakan Laragon**: Cukup start all services di Laragon. Buka browser pada URL virtual host: `http://surat.test` (atau `http://localhost/surat/public`).
- **Jika menggunakan PHP built-in server**:
  ```bash
  php artisan serve
  ```
  Buka: `http://127.0.0.1:8000`

---

## 3. KONFIGURASI WEB SERVER & DOCUMENT ROOT

> **PENTING**: Document Root domain web server **WAJIB** diarahkan ke subfolder `/public`, **BUKAN** langsung ke folder utama project Laravel.

### Mengapa Document Root Harus ke Folder `/public`?
1. **Keamanan**: Menghindari file sensitif seperti `.env`, file konfigurasi, kode PHP, dan log terbaca publik dari browser.
2. **Autentikasi & Routing**: Aset statis, route Livewire (`/livewire/update`), dan panel Filament bekerja melalui `index.php` yang berada di dalam folder `public`.

---

### A. Konfigurasi Apache (Laragon / XAMPP / cPanel)

#### 1. Laragon (Otomatis / Virtual Host)
Laragon otomatis mendeteksi document root pada folder `public`. Pastikan file virtual host di `C:\laragon\etc\apache2\sites-enabled\auto.surat.test.conf` berisi:
```apache
<VirtualHost *:80>
    DocumentRoot "E:/laragon/www/surat/public"
    ServerName surat.test
    ServerAlias *.surat.test
    <Directory "E:/laragon/www/surat/public">
        AllowOverride All
        Require all granted
    </Directory>
</VirtualHost>
```

#### 2. Apache Biasa / XAMPP
Pastikan modul `rewrite_module` aktif (`LoadModule rewrite_module modules/mod_rewrite.so` pada `httpd.conf`) dan file `public/.htaccess` bawaan Laravel tersedia:
```apache
<IfModule mod_rewrite.c>
    <IfModule mod_negotiation.c>
        Options -MultiViews -Indexes
    </IfModule>

    RewriteEngine On

    # Handle Authorization Header
    RewriteCond %{HTTP:Authorization} .
    RewriteRule .* - [E=HTTP_AUTHORIZATION:%{HTTP:Authorization}]

    # Redirect Trailing Slashes If Not A Folder...
    RewriteCond %{REQUEST_FILENAME} !-d
    RewriteCond %{REQUEST_URI} (.+)/$
    RewriteRule ^ %1 [L,R=301]

    # Send Requests To Front Controller...
    RewriteCond %{REQUEST_FILENAME} !-d
    RewriteCond %{REQUEST_FILENAME} !-f
    RewriteRule ^ index.php [L]
</IfModule>
```

---

### B. Konfigurasi Nginx (Ubuntu / VPS)
Jika Anda menggunakan Nginx, gunakan konfigurasi server block berikut:
```nginx
server {
    listen 80;
    server_name surat.lamongankab.go.id;
    root /var/www/surat/public;

    add_header X-Frame-Options "SAMEORIGIN";
    add_header X-Content-Type-Options "nosniff";

    index index.php;

    charset utf-8;

    location / {
        try_files $uri $uri/ /index.php?$query_string;
    }

    location = /favicon.ico { access_log off; log_not_found off; }
    location = /robots.txt  { access_log off; log_not_found off; }

    error_page 404 /index.php;

    location ~ \.php$ {
        fastcgi_pass unix:/var/run/php/php8.2-fpm.sock;
        fastcgi_param SCRIPT_FILENAME $realpath_root$fastcgi_script_name;
        include fastcgi_params;
    }

    location ~ /\.(?!well-known).* {
        deny all;
    }
}
```

---

## 4. AKUN LOGIN BAWAAN (DEFAULT CREDENTIALS)

Setelah menjalankan `php artisan db:seed --class=JabatanUserSeeder`, seluruh akun berikut siap digunakan pada portal login admin (`/admin/login`):

| Nama Akun / Jabatan | Email Login | Password | Peran (Role) |
|---|---|---|---|
| **Administrator Sistem** | `admin@lamongan.go.id` | `admin123` | `superadmin` |
| **Sekretaris Kecamatan** | `sekcam@lamongan.go.id` | `password123` | `jabatan` |
| **Kasi Pemerintahan** | `kasi.pemerintahan@lamongan.go.id` | `password123` | `jabatan` |
| **Kasi Pelayanan Publik** | `kasi.pelayanan@lamongan.go.id` | `password123` | `jabatan` |
| **Kasi Ketentraman & Ketertiban Umum** | `kasi.trantibum@lamongan.go.id` | `password123` | `jabatan` |
| **Kasi Pembangunan & PM** | `kasi.ppm@lamongan.go.id` | `password123` | `jabatan` |
| **Kasubag Umum & Kepegawaian** | `kasubag.umum@lamongan.go.id` | `password123` | `jabatan` |
| **Kasubag Perencanaan Evaluasi & Keuangan**| `kasubag.pek@lamongan.go.id` | `password123` | `jabatan` |

> 🔒 **Tips Keamanan**: Untuk penggunaan produksi di kantor pemerintahan, segera ganti password akun melalui menu **Master Data > Pengguna & Jabatan**.

---

## 5. PEMECAHAN MASALAH (TROUBLESHOOTING)

### Masalah Livewire & Aset Filament Tidak Termuat
Jika halaman login tidak merespons, tombol tidak bisa diklik, atau modal tidak muncul:

1. **Jalankan pembersihan dan publikasi aset di terminal**:
   ```bash
   php artisan vendor:publish --force --tag=livewire:assets
   php artisan filament:assets
   php artisan optimize:clear
   ```
2. **Periksa file konfigurasi Livewire (`config/livewire.php`)**:
   Pastikan opsi injeksi aset Livewire bernilai `true`:
   ```php
   'inject_assets' => true,
   ```
   *(Jika sebelumnya diubah menjadi `false`, kembalikan ke `true` agar file JavaScript Livewire otomatis terinjeksi ke halaman).*

---

### Mendiagnosa Kendala Halaman Login (`/admin/login`)
Jika form login Filament tidak merespons atau terasa macet:

1. **Uji Tombol Show/Hide Password**:
   - Jika ikon mata (show/hide password) **bisa diklik dan mengubah tipe input teks**, berarti JavaScript Livewire berjalan normal.
   - Jika ikon mata **tidak bereaksi sama sekali**, aset script Livewire belum termuat (lihat langkah publish aset di atas).

2. **Periksa Tab Network di DevTools Browser (Tekan F12)**:
   - Buka DevTools (`F12`) > klik tab **Network**.
   - Masukkan email dan password, lalu klik tombol **Masuk / Login**.
   - Amati request yang dikirimkan:
     - **Status 404 pada `/livewire/update`**: Berarti konfigurasi Document Root salah atau rewrite Apache (`mod_rewrite`) belum aktif sehingga request diarahkan ke path fisik yang salah.
     - **Status 419 Page Expired (CSRF Token)**: Terjadi jika session expired atau file session lokal penuh. Jalankan `php artisan optimize:clear` dan refresh halaman.
     - **Status 500 Internal Server Error**: Buka file log di `storage/logs/laravel.log` untuk melihat pesan error detail dari database atau konfigurasi PHP.

---

### Solusi Unduh Berkas Lampiran (Mencegah 403 Forbidden)
Pada sistem ini, aksi unduh berkas lampiran surat masuk telah diperbarui menggunakan streaming download internal Laravel:
- **Metode**: Menggunakan `Storage::disk('public')->download($path)` langsung via Controller/Action PHP.
- **Keuntungan**: Tidak bergantung pada setting direktori static Apache/Nginx sehingga terbebas dari kendala permission server `403 Forbidden`.
- **Rute URL Alternatif**: Tersedia rute resmi dengan autentikasi di:
  ```
  /surat-masuk/{id}/unduh
  ```

---

### Pengisian Surat Fleksibel
Sesuai kebutuhan administrasi kedinasan:
- **Tidak ada paksaan bintang merah (`*`) pada semua kolom**: Pengguna publik maupun staf kecamatan dapat menginput surat dengan data yang tersedia tanpa terhambat validasi yang kaku.
- **Nomor Agenda Otomatis**: Jika nomor agenda tidak diisi, sistem otomatis membuat nomor agenda kedinasan sesuai pola klasifikasi surat dan instansi: `[Kode_Kategori]/[Nomor_Urut]/KEC.LMG/[Tahun]`.
- **Nomor Surat & Asal Pengirim**: Jika belum memiliki nomor resmi, sistem otomatis memberikan nilai default aman (`-`).

---

## 6. PERINTAH PEMELIHARAAN (MAINTENANCE)

Jalankan perintah-perintah berikut saat melakukan pembaruan kode atau perpindahan server:

```bash
# Membersihkan seluruh cache konfigurasi, route, view, dan compiled class
php artisan optimize:clear

# Mengoptimasi aplikasi untuk mode produksi
php artisan config:cache
php artisan route:cache
php artisan view:cache

# Mengecek status migrasi database
php artisan migrate:status
```

---

## 7. STRUKTUR DAN LISENSI

Dikembangkan untuk kepentingan kedinasan **Pemerintah Kecamatan Lamongan, Kabupaten Lamongan, Jawa Timur**.  
Hak cipta dilindungi undang-undang kedinasan dan didistribusikan sesuai ketentuan internal instansi.
