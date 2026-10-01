<?php

namespace App\Console\Commands;

use App\Models\User;
use Illuminate\Console\Command;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;

class CreateUser extends Command
{
    protected $signature = 'app:create-user {username?} {--name=} {--role=terapis}';

    protected $description = 'Buat akun admin atau terapis untuk Client Visit Tracker';

    public function handle(): int
    {
        $username = Str::lower(trim((string) ($this->argument('username') ?: $this->ask('Username'))));
        $name = trim((string) ($this->option('name') ?: $this->ask('Nama pengguna')));
        $role = (string) $this->option('role');

        if (! preg_match('/^[a-z0-9._-]{3,80}$/', $username)) {
            $this->error('Username harus 3–80 karakter dan hanya boleh berisi huruf kecil, angka, titik, garis bawah, atau tanda hubung.');
            return self::FAILURE;
        }

        if (User::where('username', $username)->exists()) {
            $this->error('Username tersebut sudah digunakan.');
            return self::FAILURE;
        }

        if ($name === '' || mb_strlen($name) > 150 || ! in_array($role, ['admin', 'terapis'], true)) {
            $this->error('Nama wajib diisi (maksimal 150 karakter) dan role harus admin atau terapis.');
            return self::FAILURE;
        }

        $password = $this->secret('Password (minimal 12 karakter)');
        if (! is_string($password) || mb_strlen($password) < 12) {
            $this->error('Password harus minimal 12 karakter.');
            return self::FAILURE;
        }

        User::create([
            'name' => $name,
            'username' => $username,
            'password' => $password,
            'role' => $role,
        ]);

        $this->info("Akun {$role} {$username} berhasil dibuat.");

        return self::SUCCESS;
    }
}
