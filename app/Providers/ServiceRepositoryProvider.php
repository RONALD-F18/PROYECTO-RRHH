<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;

// Módulo Auth / Usuarios
use App\Repositories\Interfaces\AuthInterface;
use App\Repositories\Interfaces\PasswordResetInterface;
use App\Repositories\Interfaces\RolInterface;
use App\Repositories\Interfaces\UsuarioInterface;
use App\Repositories\Eloquent\AuthRepository;
use App\Repositories\Eloquent\PasswordResetRepository;
use App\Repositories\Eloquent\RolRepository;
use App\Repositories\Eloquent\UsuarioRepository;
use App\Services\MailService;
use App\Services\PasswordResetService;

// Módulo Empleado (empleado, banco)
use App\Repositories\Interfaces\BancoInterface;
use App\Repositories\Interfaces\EmpleadoInterface;
use App\Repositories\Eloquent\BancoRepository;
use App\Repositories\Eloquent\EmpleadoRepository;

// Módulo Contrato (contrato, cargo)
use App\Repositories\Interfaces\CargoInterface;
use App\Repositories\Interfaces\ContratoInterface;
use App\Repositories\Eloquent\CargoRepository;
use App\Repositories\Eloquent\ContratoRepository;

// Módulo Afiliaciones (EPS, caja de compensación, ARL, pensión, cesantías, riesgo, afiliación)
use App\Repositories\Interfaces\AfiliacionInterface;
use App\Repositories\Interfaces\ArlInterface;
use App\Repositories\Interfaces\CesantiaInterface;
use App\Repositories\Interfaces\CompensacionInterface;
use App\Repositories\Interfaces\EpsInterface;
use App\Repositories\Interfaces\PensionInterface;
use App\Repositories\Interfaces\RiesgoInterface;
use App\Repositories\Eloquent\AfiliacionesRepository;
use App\Repositories\Eloquent\ArlRepository;
use App\Repositories\Eloquent\CesantiaRepository;
use App\Repositories\Eloquent\CompensacionRepository;
use App\Repositories\Eloquent\EpsRepository;
use App\Repositories\Eloquent\PensionRepository;
use App\Repositories\Eloquent\RiesgoRepository;

// Módulo Inasistencia (inasistencia)
use App\Repositories\Interfaces\InasistenciaInterface;
use App\Repositories\Eloquent\InasistenciaRepository;

// Módulo Prestaciones Sociales
use App\Repositories\Interfaces\PrestacionSocialInterface;
use App\Repositories\Eloquent\PrestacionSocialRepository;

// Módulo Incapacidades (incapacidad, tipo_incapacidad, clasificacion_enfermedad)
use App\Repositories\Interfaces\IncapacidadInterface;
use App\Repositories\Interfaces\TipoIncapacidadInterface;
use App\Repositories\Interfaces\ClasificacionEnfermedadInterface;
use App\Repositories\Eloquent\IncapacidadRepository;
use App\Repositories\Eloquent\TipoIncapacidadRepository;
use App\Repositories\Eloquent\ClasificacionEnfermedadRepository;

//Módulo Comunicaciones Disciplinarias
use App\Repositories\Interfaces\ComunicacionDisciplinariaInterface;
use App\Repositories\Eloquent\ComunicacionDisciplinariaRepository;



class ServiceRepositoryProvider extends ServiceProvider
{
    /**
     * Register services.
     *
     * @return void
     */
    public function register()
    {
        // Módulo Auth / Usuarios
        $this->app->bind(RolInterface::class, RolRepository::class);
        $this->app->bind(UsuarioInterface::class, UsuarioRepository::class);
        $this->app->bind(AuthInterface::class, AuthRepository::class);
        $this->app->bind(PasswordResetInterface::class, PasswordResetRepository::class);
        $this->app->bind(MailService::class, fn () => new MailService());
        $this->app->bind(PasswordResetService::class, fn ($app) => new PasswordResetService(
            $app->make(MailService::class),
            $app->make(PasswordResetInterface::class)
        ));

        // Módulo Empleado (empleado, banco)
        $this->app->bind(EmpleadoInterface::class, EmpleadoRepository::class);
        $this->app->bind(BancoInterface::class, BancoRepository::class);

        // Módulo Contrato (contrato, cargo)
        $this->app->bind(ContratoInterface::class, ContratoRepository::class);
        $this->app->bind(CargoInterface::class, CargoRepository::class);

        // Módulo Afiliaciones (EPS, caja compensación, ARL, pensión, cesantías, riesgo, afiliación)
        $this->app->bind(EpsInterface::class, EpsRepository::class);
        $this->app->bind(RiesgoInterface::class, RiesgoRepository::class);
        $this->app->bind(ArlInterface::class, ArlRepository::class);
        $this->app->bind(PensionInterface::class, PensionRepository::class);
        $this->app->bind(CesantiaInterface::class, CesantiaRepository::class);
        $this->app->bind(CompensacionInterface::class, CompensacionRepository::class);
        $this->app->bind(AfiliacionInterface::class, AfiliacionesRepository::class);

        // Módulo Inasistencia
        $this->app->bind(InasistenciaInterface::class, InasistenciaRepository::class);

        // Módulo Prestaciones Sociales
        $this->app->bind(PrestacionSocialInterface::class, PrestacionSocialRepository::class);

        // Módulo Incapacidades
        $this->app->bind(IncapacidadInterface::class, IncapacidadRepository::class);
        $this->app->bind(TipoIncapacidadInterface::class, TipoIncapacidadRepository::class);
        $this->app->bind(ClasificacionEnfermedadInterface::class, ClasificacionEnfermedadRepository::class);

        // Módulo Comunicaciones Disciplinarias
        $this->app->bind(ComunicacionDisciplinariaInterface::class, ComunicacionDisciplinariaRepository::class);
        
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
