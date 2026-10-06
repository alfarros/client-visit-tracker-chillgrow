<?php

namespace App\Providers;

use App\Models\User;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        //
    }

    public function boot(): void
    {
        // Login attempts are rate limited after failed authentication only.
        Gate::define('manage-users', fn (User $pengguna): bool => $pengguna->isSuperAdmin());
        Gate::define('update-user-profile', fn (User $penggunaLogin, User $pengguna): bool =>
            $penggunaLogin->isSuperAdmin() || $penggunaLogin->is($pengguna));
    }
}
