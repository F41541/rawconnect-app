<?php

namespace App\Providers;

use App\Models\Kategori; // 
use Illuminate\Pagination\Paginator;
use Illuminate\Support\Facades\View; // 
use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Schema;

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

        try {
            if (Schema::hasTable('kategoris')) {
                View::composer('components.sidebar', function ($view) {
                    $sidebarKategoris = Kategori::with(['jenisProduks' => function ($query) {
                        $query->whereHas('produks')->withCount('produks')->orderBy('name', 'asc');
                    }])
                    ->oldest()
                    ->get();

                    $view->with('sidebarKategoris', $sidebarKategoris);
                });
            }
        } catch (\Exception $e) {
            // Jika database belum siap, abaikan saja, jangan error
        }
    }
}
