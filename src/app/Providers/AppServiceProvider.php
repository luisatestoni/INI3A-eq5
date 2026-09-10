<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\View;
use Illuminate\Support\Facades\Auth;

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
        View::composer('*', function ($view) {
            $totalNaoLidas = Auth::check() 
                ? Auth::user()->unreadNotifications->count() // Ou a sua lógica de banco
                : 0;

            $view->with('totalNaoLidas', $totalNaoLidas);
        });
    }
}
