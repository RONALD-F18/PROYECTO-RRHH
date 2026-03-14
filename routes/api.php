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

// Inasistencias
use App\Http\Controllers\InasistenciaController;

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

        // Catálogos de afiliaciones
        Route::apiResource('eps', EpsController::class);
        Route::apiResource('riesgos', RiesgoController::class);
        Route::apiResource('arls', ArlController::class);
        Route::apiResource('pensiones', PensionController::class);
        Route::apiResource('cesantias', CesantiaController::class);
        Route::apiResource('compensaciones', CompensacionController::class);
        Route::apiResource('afiliaciones', AfiliacionController::class);

        // ——— Solo administrador: gestión de usuarios y roles ———
        Route::middleware('role:administrador')->group(function () {
            Route::get('usuarios', [UsuarioController::class, 'index'])->name('usuarios.index');
            Route::post('usuarios', [UsuarioController::class, 'store'])->name('usuarios.store');
            Route::delete('usuarios/{usuario}', [UsuarioController::class, 'destroy'])->name('usuarios.destroy');
            Route::apiResource('roles', RolController::class);
        });
    });
});
