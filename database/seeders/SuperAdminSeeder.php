<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use RuntimeException;

class SuperAdminSeeder extends Seeder
{
    public function run(): void
    {
        $nama = config('admin.super_admin_name');
        $username = config('admin.super_admin_username');
        $kataSandi = config('admin.super_admin_password');

        if (! is_string($nama) || trim($nama) === '' || mb_strlen($nama) > 150) {
            throw new RuntimeException('SUPER_ADMIN_NAME wajib diisi dan maksimal 150 karakter.');
        }

        if (! is_string($username) || ! preg_match('/^[a-z0-9._-]{3,80}$/', $username)) {
            throw new RuntimeException('SUPER_ADMIN_USERNAME harus 3–80 karakter: huruf kecil, angka, titik, garis bawah, atau strip.');
        }

        if (! is_string($kataSandi) || mb_strlen($kataSandi) < 12) {
            throw new RuntimeException('Isi SUPER_ADMIN_PASSWORD di .env dengan kata sandi minimal 12 karakter.');
        }

        $pengguna = User::firstOrCreate(
            ['username' => $username],
            ['name' => trim($nama), 'password' => $kataSandi, 'role' => 'super_admin'],
        );

        if (! $pengguna->wasRecentlyCreated && ! $pengguna->isSuperAdmin()) {
            throw new RuntimeException("Username {$username} sudah dipakai akun yang bukan super_admin. Pilih SUPER_ADMIN_USERNAME yang lain.");
        }

        $this->command?->info($pengguna->wasRecentlyCreated
            ? "Akun super_admin {$username} berhasil dibuat."
            : "Akun super_admin {$username} sudah ada; data login tidak diubah.");
    }
}
