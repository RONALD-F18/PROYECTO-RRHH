<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use App\Repositories\Interfaces\RolInterface;
use App\Repositories\Eloquent\RolRepository;
use App\Repositories\Interfaces\UsuarioInterface;
use App\Repositories\Eloquent\UsuarioRepository;


class ServiceRepositoryProvider extends ServiceProvider
{
    /**
     * Register services.
     *
     * @return void
     */
    public function register()
    {
        $this->app->bind(RolInterface::class, RolRepository::class);
        $this->app->bind(UsuarioInterface::class, UsuarioRepository::class);
    }

    /**
     * Bootstrap services.
     *
     * @return void
     */
    public function boot()
    {
        //
    }
}
