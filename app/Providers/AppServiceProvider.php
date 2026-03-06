<?php

namespace App\Providers;

use Illuminate\Support\Facades\Gate;
use Illuminate\Support\ServiceProvider;
use App\Models\User;

class AppServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        //
    }

    public function boot(): void
    {
        Gate::define('admin-only', function(User $user){
            return $user->role === 'admin';
        });

        Gate::define('user-only', function(User $user){
            return $user->role === 'user';
        });

    }
}