<?php

namespace App\Providers;

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
        \Illuminate\Support\Facades\Blade::if('readonly', function () {
            return session('is_read_only', false)
                || session('user_role') === 'manager'
                || (auth()->check() && auth()->user()->role === 'manager');
        });

        \Illuminate\Support\Facades\Blade::if('canwrite', function () {
            return !(session('is_read_only', false)
                || session('user_role') === 'manager'
                || (auth()->check() && auth()->user()->role === 'manager'));
        });
    }
}
