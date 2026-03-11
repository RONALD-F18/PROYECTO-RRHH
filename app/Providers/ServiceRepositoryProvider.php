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
use App\Repositories\Interfaces\PasswordResetInterface;     
use App\Repositories\Eloquent\PasswordResetRepository;       
use App\Repositories\Interfaces\BancoInterface;
use App\Repositories\Eloquent\BancoRepository;
use App\Repositories\Interfaces\EmpleadoInterface;
use App\Repositories\Eloquent\EmpleadoRepository;
use App\Repositories\Interfaces\EpsInterface;
use App\Repositories\Eloquent\EpsRepository;
use App\Repositories\Interfaces\RiesgoInterface;
use App\Repositories\Eloquent\RiesgoRepository;
use App\Repositories\Interfaces\ArlInterface;
use App\Repositories\Eloquent\ArlRepository;
use App\Repositories\Interfaces\PensionInterface;
use App\Repositories\Eloquent\PensionRepository;
use App\Repositories\Interfaces\CesantiaInterface;
use App\Repositories\Eloquent\CesantiaRepository;
use App\Repositories\Interfaces\CompensacionInterface;
use App\Repositories\Eloquent\CompensacionRepository;
use App\Repositories\Interfaces\AfiliacionInterface;
use App\Repositories\Eloquent\AfiliacionesRepository;



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
        $this->app->bind(PasswordResetInterface::class, PasswordResetRepository::class);

        $this->app->bind(MailService::class, fn() => new MailService());
        $this->app->bind(PasswordResetService::class, fn($app) => new PasswordResetService(
            $app->make(MailService::class),
            $app->make(PasswordResetInterface::class)
        ));

        $this->app->bind(BancoInterface::class, BancoRepository::class);
        $this->app->bind(EmpleadoInterface::class, EmpleadoRepository::class);   
        
        $this->app->bind(EpsInterface::class, EpsRepository::class);
        $this->app->bind(RiesgoInterface::class, RiesgoRepository::class);
        $this->app->bind(ArlInterface::class, ArlRepository::class);
        $this->app->bind(PensionInterface::class, PensionRepository::class);
        $this->app->bind(CesantiaInterface::class, CesantiaRepository::class);
        $this->app->bind(CompensacionInterface::class, CompensacionRepository::class);
        $this->app->bind(AfiliacionInterface::class, AfiliacionesRepository::class);

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
