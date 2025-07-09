<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Gate;
use App\Models\User;

class AuthServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap services.
     */
    public function boot(): void
    {
        // Daftarkan Gate baru kita untuk memeriksa peran pengguna

        // Gate ini akan bernilai TRUE hanya jika peran pengguna adalah 'super-admin'
        Gate::define('is-super-admin', function (User $user) {
            return $user->role === 'super-admin';
        });

        // Gate ini akan bernilai TRUE jika peran pengguna adalah 'admin' ATAU 'super-admin'
        Gate::define('is-admin-or-super-admin', function (User $user) {
            return in_array($user->role, ['super-admin', 'admin']);
        });

        Gate::define('is-pegawai', function (User $user) {
            return in_array($user->role, ['pegawai']);
        });

        // Anda bisa menambahkan Gate lain di sini untuk peran 'pegawai' jika diperlukan nanti
    }
}
