<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use App\Repositories\Interfaces\RolInterface;
use App\Repositories\Eloquent\RolRepository;
use App\Repositories\Interfaces\UsuarioInterface;
use App\Repositories\Eloquent\UsuarioRepository;
use App\Repositories\Eloquent\AuthRepository;
use App\Repositories\Interfaces\AuthInterface;
use App\Services\MailService;
use App\Services\PasswordResetService;


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

        $this->app->bind(MailService::class, fn() => new MailService());
        $this->app->bind(PasswordResetService::class, fn($app) => new PasswordResetService(
            $app->make(MailService::class)
        ));
    }

    /**
     * Bootstrap services.
     *
     * @return void
     */
    public function boot()
    {
        
    }
}
