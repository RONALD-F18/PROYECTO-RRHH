<?php

use Illuminate\Support\Facades\Route;

// Auth y usuarios
use App\Http\Controllers\AuthController;
use App\Http\Controllers\UsuarioController;
use App\Http\Controllers\RolController;
use App\Http\Controllers\PasswordResetController;

// Empleados, bancos, cargos, contratos
use App\Http\Controllers\BancoController;
use App\Http\Controllers\CargoController;
use App\Http\Controllers\ContratoController;
use App\Http\Controllers\EmpleadoController;

// Afiliaciones y catálogos
use App\Http\Controllers\AfiliacionController;
use App\Http\Controllers\ArlController;
use App\Http\Controllers\CesantiaController;
use App\Http\Controllers\CompensacionController;
use App\Http\Controllers\EpsController;
use App\Http\Controllers\PensionController;
use App\Http\Controllers\RiesgoController;
use App\Http\Controllers\ActividadCalendarioController;
use App\Http\Controllers\EmpresaController;
use App\Http\Controllers\CertificacionController;


// Inasistencias
use App\Http\Controllers\InasistenciaController;

// Prestaciones sociales
use App\Http\Controllers\PrestacionSocialController;

// Incapacidades (entidad principal y catálogos por capas)
use App\Http\Controllers\IncapacidadController;
use App\Http\Controllers\TipoIncapacidadController;
use App\Http\Controllers\ClasificacionEnfermedadController;

// Comunicaciones disciplinarias
use App\Http\Controllers\ComunicacionDisciplinariaController;

// Reportes generales RRHH
use App\Http\Controllers\ReporteController;

Route::prefix('v1')->group(function () {

    // ——— Públicas (sin autenticación) ———
    Route::post('/login', [AuthController::class, 'login']);
    Route::post('/forgot-password', [PasswordResetController::class, 'sendResetLink']);
    Route::post('/reset-password', [PasswordResetController::class, 'resetPassword']);

    // ——— Requieren autenticación (administrador y funcionario) ———
    Route::middleware('auth.api')->group(function () {

        Route::post('/logout', [AuthController::class, 'logout']);

        // Cualquier autenticado: ver y editar su propio perfil (policy valida que sea el mismo usuario)
        Route::get('usuarios/{usuario}', [UsuarioController::class, 'show'])->name('usuarios.show');
        Route::put('usuarios/{usuario}', [UsuarioController::class, 'update'])->name('usuarios.update');
        Route::patch('usuarios/{usuario}', [UsuarioController::class, 'update']);

        // Admin y funcionario: recursos de RRHH
        Route::apiResource('empleados', EmpleadoController::class);
        Route::apiResource('bancos', BancoController::class);
        Route::apiResource('cargos', CargoController::class);
        Route::apiResource('contratos', ContratoController::class);
        Route::apiResource('inasistencias', InasistenciaController::class);
        Route::apiResource('empresas', EmpresaController::class);
        Route::apiResource('comunicaciones_disciplinarias', ComunicacionDisciplinariaController::class);

        // Calendario de actividades
        Route::apiResource('calendario-actividades', ActividadCalendarioController::class);

        // Prestaciones sociales (resumen, por contrato, calcular, gestionar estado, eliminar)
        Route::get('prestaciones-sociales', [PrestacionSocialController::class, 'index']);
        Route::get('prestaciones-sociales/totales', [PrestacionSocialController::class, 'totalesPendientes']);
        Route::get('prestaciones-sociales/listar', [PrestacionSocialController::class, 'listarTodos']);
        Route::get('contratos/{cod_contrato}/prestaciones', [PrestacionSocialController::class, 'showByContrato']);
        Route::post('contratos/{cod_contrato}/calcular-prestaciones', [PrestacionSocialController::class, 'calcular']);
        Route::post('prestaciones-sociales/gestionar', [PrestacionSocialController::class, 'gestionar']);
        Route::delete('prestaciones-sociales/{cod_prestacion_social_periodo}', [PrestacionSocialController::class, 'destroy']);

        // Catálogos de incapacidades (cada entidad con su propia capa)
        Route::apiResource('tipos-incapacidad', TipoIncapacidadController::class);
        Route::apiResource('clasificaciones-enfermedad', ClasificacionEnfermedadController::class);

        // Incapacidades (gestión y normativa colombiana de pago)
        Route::get('incapacidades/resumen', [IncapacidadController::class, 'resumen']);
        Route::get('empleados/{cod_empleado}/incapacidades', [IncapacidadController::class, 'byEmpleado']);
        Route::apiResource('incapacidades', IncapacidadController::class);

        // Catálogos de afiliaciones
        Route::apiResource('eps', EpsController::class);
        Route::apiResource('riesgos', RiesgoController::class);
        Route::apiResource('arls', ArlController::class);
        Route::apiResource('pensiones', PensionController::class);
        Route::apiResource('cesantias', CesantiaController::class);
        Route::apiResource('compensaciones', CompensacionController::class);
        Route::apiResource('afiliaciones', AfiliacionController::class);

        // Certificaciones
        Route::apiResource('certificaciones', CertificacionController::class);
        Route::get('certificaciones/{certificacion}/pdf-laboral', [CertificacionController::class, 'descargarPdfLaboral']);

        // Reportes generales RRHH (PDF)
        Route::post('reportes/generar', [ReporteController::class, 'generar'])->name('reportes.generar');

        // ——— Solo administrador: gestión de usuarios y roles ———
        Route::middleware('role:administrador')->group(function () {
            Route::get('usuarios', [UsuarioController::class, 'index'])->name('usuarios.index');
            Route::post('usuarios', [UsuarioController::class, 'store'])->name('usuarios.store');
            Route::delete('usuarios/{usuario}', [UsuarioController::class, 'destroy'])->name('usuarios.destroy');
            Route::apiResource('roles', RolController::class);
        });
    });
});
