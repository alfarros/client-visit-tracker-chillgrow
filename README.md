# Client Visit Tracker

Aplikasi internal MVP untuk mencatat jadwal kunjungan klien terapi okupasi. Laravel 12, PHP 8.2+, dan MySQL/MariaDB. Antarmuka memakai Blade dan CSS lokal tanpa proses build frontend.

## Fitur

- Login username/password, opsi Ingat Saya, dan batas lima kegagalan per username + alamat IP per menit.
- Dashboard jumlah jadwal dan daftar ringkas hari ini (zona waktu `Asia/Jakarta`).
- Periksa Pasien dengan pencarian nama/No. RM, profil, tab Lembar Program Terapi dan CPPT, serta riwayat kunjungan.
- Tabel kunjungan dengan pencarian Nama/No. RM, filter rentang tanggal, status, dan pagination.
- Aksi Selesaikan Pemeriksaan mengunci Program Terapi dan CPPT setelah keduanya tersimpan.
- Tambah, edit, serta hapus dengan dialog konfirmasi. Hapus ubahdibatasi untuk role `admin`.
- Nomor RM disimpan sebagai teks, termasuk nol di awal.

## Menyiapkan lingkungan lokal

Persyaratan: PHP 8.2+, Composer, ekstensi PDO MySQL, dan MySQL/MariaDB.

1. Salin `.env.example` ke `.env`, lalu isi `DB_DATABASE`, `DB_USERNAME`, dan `DB_PASSWORD`.
2. Jalankan `composer install`.
3. Jalankan `php artisan key:generate`.
4. Buat database kosong, lalu jalankan `php artisan migrate --force`. Migrasi normalisasi menghapus isi tabel kunjungan lama (data dummy), mengganti nama tabel menjadi `kunjungan_kliens`, lalu membuat master pasien, CPPT, dan program terapis.
5. Buat akun admin pertama: `php artisan app:create-user admin --name="Admin Klinik" --role=admin`. Masukkan password minimal 12 karakter saat diminta. Password tidak ditulis ke terminal.
6. Jalankan `php artisan serve` dan buka URL yang ditampilkan.

Akun terapis dibuat dengan perintah yang sama, misalnya `php artisan app:create-user terapis01 --name="Nama Terapis" --role=terapis`.

## Deploy ke shared hosting

Pakai PHP 8.2+ dan arahkan document root domain ke folder `public` aplikasi jika cPanel mengizinkan. Jika document root harus `public_html`, simpan seluruh proyek Laravel di luar `public_html`, salin **isi** folder `public` ke `public_html`, lalu sesuaikan path autoload dan `bootstrap/app.php` di `public_html/index.php` agar menunjuk ke proyek di luar web root. Jangan menaruh `.env`, `vendor`, `storage`, atau source aplikasi di lokasi yang dapat dilayani sebagai berkas publik.

Di `.env` production, atur `APP_ENV=production`, `APP_DEBUG=false`, `APP_URL=https://domain-klinik`, `APP_TIMEZONE=Asia/Jakarta`, `DB_*`, dan `SESSION_SECURE_COOKIE=true`. Pastikan `storage/` serta `bootstrap/cache/` dapat ditulis oleh PHP. Jalankan `php artisan migrate --force`, `php artisan optimize`, lalu buat akun melalui perintah CLI. Jangan commit `.env` atau memasukkan password awal ke source.

## Struktur data inti

Tabel domain terdiri dari `pasiens` (master), `kunjungan_kliens` (transaksi), `cppts`, dan `program_terapis`. No. RM disimpan sebagai string unik agar angka nol di awal tetap terjaga. Setiap pasien dapat memiliki banyak kunjungan; setiap kunjungan dapat memiliki banyak CPPT dan program terapis. Data `users` dan session/cache adalah kebutuhan infrastruktur Laravel.
