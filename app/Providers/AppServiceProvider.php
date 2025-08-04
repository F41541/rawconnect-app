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

    }
}
