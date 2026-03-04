<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;

use App\Models\Usuario;
use App\Policies\UserPolicy;
use Illuminate\Support\Facades\Gate;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        //
    }

    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        Gate::policy(Usuario::class, UserPolicy::class);
    }
}
