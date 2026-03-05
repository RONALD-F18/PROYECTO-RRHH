<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use App\Repositories\Interfaces\RolInterface;
use App\Repositories\Eloquent\RolRepository;
use App\Repositories\Interfaces\UsuarioInterface;
use App\Repositories\Eloquent\UsuarioRepository;
use App\Repositories\Eloquent\AuthRepository;
use App\Repositories\Interfaces\AuthInterface;
use App\Repositories\Interfaces\BancoInterface;
use App\Repositories\Eloquent\Banco_Repository;
use App\Repositories\Interfaces\EmpleadoInterface;
use App\Repositories\Eloquent\EmpleadoRepository;



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
        $this->app->bind(AuthInterface::class, AuthRepository::class);
        $this->app->bind(BancoInterface::class, Banco_Repository::class);
        $this->app->bind(EmpleadoInterface::class, EmpleadoRepository::class);   
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
