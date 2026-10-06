# Konteks Proyek untuk Agent Berikutnya

## Ringkasan
- **Nama:** Client Visit Tracker / ChilGrow EMR; aplikasi internal klinik terapi okupasi anak.
- **Stack:** Laravel 12, PHP 8.2+, MySQL/MariaDB, Blade. CSS/JS dan aset statis berada di `public/`; tidak ada kebutuhan Node build.
- **Bahasa UI:** Indonesia. Desain biru muda, bersih, responsif untuk desktop dan Safari/iOS.
- Utamakan MVP/YAGNI: jangan menambah modul medis, tabel, integrasi, atau relasi yang belum diminta.

## Fitur saat ini
- Login username/password, remember me, auth/role admin dan super admin.
- Master pasien (No. RM unik, nama, tanggal lahir, diagnosa awal), halaman profil/Periksa Pasien.
- Kunjungan terapi dengan pasien, tanggal, jam mulai (`jam_kunjungan`), jam selesai (`jam_selesai`), cara bayar Tunai/Transfer, serta status.
- Status kunjungan: `Antre`, `Berlangsung`, `Menunggu Diselesaikan`, `Selesai`, `Batal`. Status otomatis berdasarkan waktu; Selesai/Batal tidak tertimpa otomatis. Command terjadwal setiap menit.
- Selesaikan terapi mensyaratkan pengisian Lembar Program Terapi dan CPPT. Riwayat kunjungan dan tab terkait ditampilkan pada profil pasien.
- Progres bulanan pasien: refleks primitif, sensori, motorik kasar/halus, kognitif/perseptual, kemandirian; mendukung pengelolaan riwayat.
- Arsip dokumen pasien di private storage (`storage/app/private`), dengan preview modal PDF.js lokal, download, dan hapus.
- PWA kosmetik untuk Home Screen, **tanpa service worker** sesuai instruksi pengguna.

## Logika & implementasi kunjungan terbaru
- Migration terbaru: `2026_10_07_000000_add_jam_selesai_and_waiting_status_to_kunjungan_kliens_table.php` menambah `jam_selesai` nullable dan status baru.
- Store/update request memvalidasi jam format `H:i`, wajib, dan jam selesai harus setelah jam mulai.
- `KunjunganKlienController::hitungStatusOtomatis()` menentukan Antre sebelum jadwal, Berlangsung di dalam rentang, Menunggu Diselesaikan setelah rentang; status terminal dipertahankan.
- Command `StartScheduledVisits` dijadwalkan melalui `routes/console.php` setiap menit. cPanel perlu cron `php artisan schedule:run` tiap menit (sesuaikan path PHP/artisan hosting).
- Aksi complete menerima status Berlangsung atau Menunggu Diselesaikan.

## Deployment / operasi
- Tujuan hosting yang dibahas: cPanel; domain `emr-chilgrow.web.id` dan SSL pernah terlihat aktif. Akses SSH/terminal paket belum terkonfirmasi; minta host/port/username/key dan Composer ke provider, jangan menebak dari shared IP.
- Deploy Laravel dengan document root menunjuk ke folder `public`; letakkan core Laravel di luar web root bila struktur hosting memungkinkan. `.env` produksi: `APP_DEBUG=false`, `APP_ENV=production`, `APP_URL` benar, DB kredensial production. Rahasiakan `.env` dan password.
- Seeder admin/super admin memakai `ADMIN_PASSWORD` dan `SUPER_ADMIN_PASSWORD` (minimum 12 karakter); jangan tulis kredensial nyata di repo atau jawaban.
- Jalankan scheduler melalui cron cPanel setiap menit agar status waktu berjalan otomatis.
- PWA: `public/manifest.json`, meta tags layout/login, ikon ada di `public/icons/`. Tidak boleh mendaftarkan Service Worker.

## Riwayat keputusan migration
- Proyek masih tahap uji; pengguna meminta perubahan langsung pada migration dasar untuk cara bayar/status dan refleks primitif, bukan menumpuk migration alter. Lihat `2026_10_01_100000_normalize_patient_visit_records.php` dan `2026_10_03_000000_create_evaluasi_bulanans_table.php`.
- Untuk perubahan waktu/status terbaru sudah ada migration baru tanggal 7 Oktober.
- Jangan jalankan `migrate:fresh` pada database yang berisi data. Gunakan migration biasa; sebelum mengubah sejarah migration, cek lingkungan dan data terlebih dahulu.

## Aturan kerja penting
1. Mulai dengan `git status` dan tinjau diff; repo berisi perubahan yang mungkin milik pengguna. Jangan reset, checkout, atau membuang perubahan tanpa instruksi.
2. Ikuti pola controller, request, model, route, Blade, CSS yang sudah ada; gunakan label Bahasa Indonesia.
3. Jaga data dokumen tetap private dan semua route pasien/dokumen terproteksi auth/otorisasi yang sudah berlaku.
4. Jangan menambahkan Service Worker.
5. Jangan menjalankan test atau perintah destruktif kecuali diminta. Pemeriksaan sintaks/build yang aman boleh dilakukan bila relevan.
6. Perubahan lokal terakhir pada fitur rentang waktu sudah pernah dicek syntax PHP, `view:cache`, dan migration lokal; test suite belum dijalankan.

## Titik awal kode
- Model: `app/Models/` (Pasien, KunjunganKlien, EvaluasiBulanan, DokumenPasien, CPPT, ProgramTerapis).
- Controller & Form Requests: `app/Http/Controllers/`, `app/Http/Requests/`.
- Scheduler/command: `routes/console.php`, `app/Console/Commands/`.
- Routes: `routes/web.php`.
- Blade: `resources/views/` (layout, kunjungan, pasien).
- Migrations/seeders: `database/migrations/`, `database/seeders/`.
