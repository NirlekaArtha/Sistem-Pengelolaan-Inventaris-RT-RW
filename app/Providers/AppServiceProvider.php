<?php

namespace App\Providers;

use App\Models\Barang;
use App\Models\StokBarang;
use App\Observers\BarangObserver;
use App\Observers\StokBarangObserver;
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
        Barang::observe(BarangObserver::class);
        StokBarang::observe(StokBarangObserver::class);
    }
}
