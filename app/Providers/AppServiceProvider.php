<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
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
        // Paksa HTTPS jika APP_URL menggunakan https (Cocok untuk Ngrok/Localtunnel)
        if (str_contains(config('app.url'), 'https://')) {
            URL::forceScheme('https');
        }

        \Illuminate\Pagination\Paginator::useBootstrapFive();
    }
}
