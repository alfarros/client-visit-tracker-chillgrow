# Client Visit Tracker

Aplikasi internal MVP untuk mencatat jadwal kunjungan klien terapi okupasi. Laravel 12, PHP 8.2+, dan MySQL/MariaDB. Antarmuka memakai Blade dan CSS lokal tanpa proses build frontend.

## Fitur

- Login username/password, opsi Ingat Saya, dan batas lima kegagalan per username + alamat IP per menit.
- Dashboard jumlah jadwal dan daftar ringkas hari ini (zona waktu `Asia/Jakarta`).
- Periksa Pasien dengan pencarian nama/No. RM, profil, tab Lembar Program Terapi dan CPPT, serta riwayat kunjungan.
- Tabel kunjungan dengan pencarian Nama/No. RM, filter rentang tanggal, status, dan pagination.
- Status berubah otomatis dari `Antre` ke `Berlangsung` saat jam jadwal terapi tiba; aksi Selesaikan Terapi mengunci Program Terapi dan CPPT setelah keduanya tersimpan.
- Riwayat Progres Bulanan dengan CRUD lima aspek dan Arsip Dokumen Medis privat (PDF/JPG/PNG hingga 10 MB) dengan preview, download, dan hapus.
- Manajemen akun berbasis peran: `super_admin` mengelola semua akun; `admin` hanya mengubah profil sendiri.
- Nomor RM disimpan sebagai teks, termasuk nol di awal.

## Menyiapkan lingkungan lokal

Persyaratan: PHP 8.2+, Composer, ekstensi PDO MySQL, dan MySQL/MariaDB.

1. Salin `.env.example` ke `.env`, lalu isi `DB_DATABASE`, `DB_USERNAME`, dan `DB_PASSWORD`.
2. Jalankan `composer install`.
3. Jalankan `php artisan key:generate`.
4. Isi `ADMIN_NAME`, `ADMIN_USERNAME`, dan `ADMIN_PASSWORD` serta `SUPER_ADMIN_NAME`, `SUPER_ADMIN_USERNAME`, dan `SUPER_ADMIN_PASSWORD` di `.env`. Gunakan password unik minimal 12 karakter.
5. Buat database kosong, lalu jalankan `php artisan migrate --force`. Migrasi normalisasi menghapus isi tabel kunjungan lama (data dummy), mengganti nama tabel menjadi `kunjungan_kliens`, lalu membuat master pasien, CPPT, program terapis, dan evaluasi bulanan.
6. Buat akun admin dan super admin dengan `php artisan db:seed --force`. Untuk database lokal kosong yang ingin dibuat ulang sekaligus diisi kedua akun, gunakan `php artisan migrate:fresh --seed` (perintah ini menghapus seluruh tabel dan data lebih dulu).
7. Jalankan `php artisan serve` dan buka URL yang ditampilkan.

Untuk menyiapkan akun `super_admin` pertama, gunakan `php artisan app:create-user pemilik --name="Nama Pemilik" --role=super_admin`. Akun admin biasa dibuat dengan perintah yang sama tanpa opsi `--role`, atau dengan `--role=admin`.

## Deploy ke shared hosting

Pakai PHP 8.2+ dan arahkan document root domain ke folder `public` aplikasi jika cPanel mengizinkan. Jika document root harus `public_html`, simpan seluruh proyek Laravel di luar `public_html`, salin **isi** folder `public` ke `public_html`, lalu sesuaikan path autoload dan `bootstrap/app.php` di `public_html/index.php` agar menunjuk ke proyek di luar web root. Jangan menaruh `.env`, `vendor`, `storage`, atau source aplikasi di lokasi yang dapat dilayani sebagai berkas publik.

Di `.env` production, atur `APP_ENV=production`, `APP_DEBUG=false`, `APP_URL=https://domain-klinik`, `APP_TIMEZONE=Asia/Jakarta`, `DB_*`, `SESSION_SECURE_COOKIE=true`, dan kredensial `ADMIN_*` serta `SUPER_ADMIN_*`. Pastikan `storage/` serta `bootstrap/cache/` dapat ditulis oleh PHP. Jalankan `php artisan migrate --force`, `php artisan db:seed --force`, lalu `php artisan optimize`. Jangan jalankan `migrate:fresh` pada database production. Jangan commit `.env` atau memasukkan password awal ke source.

#### Cron Job cPanel untuk status otomatis

Cron Job adalah pemicu berkala di server. Di cPanel, buka **Cron Jobs**, lalu buat job baru dengan semua pilihan waktu diatur ke `*` (setiap menit). Pada kolom **Command**, isi perintah berikut dan sesuaikan path folder proyek serta lokasi PHP dari provider hosting:

```sh
cd /home/USERNAME/client-visit-tracker && /usr/local/bin/php artisan schedule:run >> /dev/null 2>&1
```

Path `/home/USERNAME/client-visit-tracker` harus menunjuk ke folder Laravel yang berisi file `artisan`, bukan folder `public`. Lokasi PHP dapat berbeda di tiap hosting; tanyakan ke provider atau cek melalui Terminal cPanel. Setelah aktif, cPanel memanggil Laravel tiap menit; Laravel lalu menjalankan perintah yang mengubah kunjungan hari ini menjadi `Berlangsung` saat jam jadwalnya tiba. Kunjungan lama tanpa jam tetap berstatus sesuai data saat ini sampai jamnya diisi.

Untuk development lokal, jalankan `php artisan schedule:work` di terminal terpisah selama aplikasi dipakai.

## Struktur data inti

Tabel domain terdiri dari `pasiens` (master), `kunjungan_kliens` (transaksi), `cppts`, dan `program_terapis`. No. RM disimpan sebagai string unik agar angka nol di awal tetap terjaga. Setiap pasien dapat memiliki banyak kunjungan; setiap kunjungan dapat memiliki banyak CPPT dan program terapis. Data `users` dan session/cache adalah kebutuhan infrastruktur Laravel.
