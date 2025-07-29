<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\URL;

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
        // MySQL için string uzunluk sınırlaması
        Schema::defaultStringLength(191);
        
        // HTTPS zorunluluğu (yayında)
        if (config('app.env') === 'production') {
            URL::forceScheme('https');
        }
    }
}
