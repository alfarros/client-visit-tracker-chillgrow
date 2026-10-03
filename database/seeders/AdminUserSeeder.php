<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use RuntimeException;

class AdminUserSeeder extends Seeder
{
    public function run(): void
    {
        $name = config('admin.name');
        $username = config('admin.username');
        $password = config('admin.password');

        if (! is_string($name) || trim($name) === '' || mb_strlen($name) > 150) {
            throw new RuntimeException('ADMIN_NAME wajib diisi dan maksimal 150 karakter.');
        }

        if (! is_string($username) || ! preg_match('/^[a-z0-9._-]{3,80}$/', $username)) {
            throw new RuntimeException('ADMIN_USERNAME harus 3–80 karakter: huruf kecil, angka, titik, garis bawah, atau strip.');
        }

        if (! is_string($password) || mb_strlen($password) < 12) {
            throw new RuntimeException('Isi ADMIN_PASSWORD di .env dengan password minimal 12 karakter sebelum menjalankan seeder.');
        }

        $admin = User::firstOrCreate(
            ['username' => $username],
            ['name' => trim($name), 'password' => $password, 'role' => 'admin'],
        );

        $this->command?->info($admin->wasRecentlyCreated
            ? "Akun admin {$username} berhasil dibuat."
            : "Akun {$username} sudah ada; data login tidak diubah.");
    }
}
