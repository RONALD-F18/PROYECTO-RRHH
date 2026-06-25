<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Cache\RateLimiting\Limit;
use Illuminate\Http\Request;

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

        RateLimiter::for('api', function (Request $request) {
            return Limit::perMinute(20)->by($request->user()?->cod_usuario ?: $request->ip());
        });

        RateLimiter::for('login', function (Request $request) {
            return Limit::every('3 seconds')->by($request->ip());
        });
    }
}
