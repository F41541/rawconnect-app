<?php

namespace App\Providers;

use App\Models\Kategori; // 
use Illuminate\Pagination\Paginator;
use Illuminate\Support\Facades\View; // 
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        Paginator::useBootstrapFive();

        // Bagikan data Kategori ke view sidebar secara global
        View::composer('components.sidebar', function ($view) {
            // Hapus whereHas di awal untuk mengambil semua kategori
            $sidebarKategoris = Kategori::with(['jenisProduks' => function ($query) {
                // Tapi kita tetap hanya ingin menampilkan Jenis Produk yang ada isinya
                $query->whereHas('produks')->withCount('produks')->orderBy('name', 'asc');
            }])
            ->oldest()
            ->get();

            $view->with('sidebarKategoris', $sidebarKategoris);
        });
    }
}
