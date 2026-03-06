<?php

namespace App\Providers;

use Illuminate\Support\Facades\Gate;
use Illuminate\Support\ServiceProvider;

class AuthServiceProvider extends ServiceProvider
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
        // Definir la Gate para administradores, Método que se ejecuta al arrancar Laravel
        Gate::define('admin-only', function ($user) {    //Recibe el usuario autenticado
            return $user->role === 'admin';             // Devuelve true si el rol es 'admin'
        });
    }
}