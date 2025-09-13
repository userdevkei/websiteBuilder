<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Schema;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        Schema::defaultStringLength(191);
    }

    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        // Add this helper for admin assets
        app()->singleton('admin.asset', function () {
            return function ($path) {
                $base = app()->environment('local')
                    ? 'backend/assets/admin'
                    : 'public/backend/assets/admin';
                return asset($base . '/' . ltrim($path, '/'));
            };
        });
    }
}
