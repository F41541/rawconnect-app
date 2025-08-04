<?php

namespace App\Providers;

use App\Models\User;
use App\Models\PratinjauItem;
use Illuminate\Support\Facades\Gate;
use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;

class AuthServiceProvider extends ServiceProvider
{
    /**
     * The model to policy mappings for the application.
     *
     * @var array<class-string, class-string>
     */
    protected $policies = [
        //
    ];

    /**
     * Register any authentication / authorization services.
     */
public function boot(): void
{
    // Izin untuk mengelola data master & pengguna (Hanya Super Admin)
    Gate::define('is-super-admin', function (User $user) {
        return $user->role === 'super-admin';
    });
    
    // Izin untuk membuat pengiriman & membatalkannya (Admin & Super Admin)
    Gate::define('manage-shipments', function (User $user) {
        return in_array($user->role, ['super-admin', 'admin']);
    });

    Gate::define('create-shipments', function (User $user) {
        return in_array($user->role, ['super-admin', 'admin']);
    });

    Gate::define('cancel-shipment', function (User $user) {
        return in_array($user->role, ['super-admin', 'admin']);
    });

    // Izin untuk melakukan penyesuaian stok (Pegawai & Admin)
    Gate::define('adjust-stock', function (User $user) {
        return in_array($user->role, ['admin', 'pegawai']);
    });

    // Izin untuk mengubah status (semua peran)
    Gate::define('update-status', function (User $user) {
        return in_array($user->role, ['super-admin', 'admin', 'pegawai']);
    });

    Gate::define('all', function (User $user) {
        return in_array($user->role, ['super-admin', 'admin', 'pegawai']);
    });

    // Izin untuk update item di pratinjau (hanya pemilik)
    Gate::define('update-pratinjau-item', function (User $user, PratinjauItem $pratinjauItem) {
        return $user->id === $pratinjauItem->user_id;
    });
}
}