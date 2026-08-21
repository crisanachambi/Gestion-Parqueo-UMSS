<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\View;

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
        // El header y el sidebar muestran el parqueo del
        // encargado en TODAS las pantallas. En vez de pasarlo
        // desde cada controlador, se comparte una sola vez.
        View::composer(['layouts.header', 'layouts.sidebar'], function ($view) {
            $view->with('parqueoActivo', auth()->user()?->parqueoAsignado);
        });
    }
}
