<?php

namespace App\Providers;

use App\Models\Kategori; // 
use Illuminate\Pagination\Paginator;
use Illuminate\Support\Facades\View; // 
use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Cache; 

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

        View::composer('components.sidebar', function ($view) {
            $sidebarKategoris = Cache::remember('sidebar_kategoris', 3600, function () {
                return Kategori::with(['jenisProduks' => function ($query) {
                    $query->whereHas('produks')->withCount('produks')->orderBy('name', 'asc');
                }])
                ->oldest()
                ->get();
            });

            $view->with('sidebarKategoris', $sidebarKategoris);
        });
    }
}
