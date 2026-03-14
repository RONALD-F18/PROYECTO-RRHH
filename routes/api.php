<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\UsuarioController;
use App\Http\Controllers\RolController;
use App\Http\Controllers\PasswordResetController; // ← agregar este import
use App\Http\Controllers\BancoController;
use App\Http\Controllers\EmpleadoController;
use App\Http\Controllers\CargoController;
use App\Http\Controllers\ContratoController;

Route::prefix('v1')->group(function () {

    Route::post('/forgot-password', [PasswordResetController::class, 'sendResetLink']);
    Route::post('/reset-password',  [PasswordResetController::class, 'resetPassword']);
    Route::apiResource('contratos', ContratoController::class);


    Route::post('/login', [AuthController::class, 'login']);


    // Rutas protegidas con JWT
    Route::middleware('auth.api')->group(function () {

        Route::post('/logout', [AuthController::class, 'logout']);

        //Empleados
        //Cualquier usuario autenticado puede trbajar con esto:
        Route::apiResource('empleados', EmpleadoController::class);
        Route::apiResource('bancos', BancoController::class);
        Route::apiResource('cargos', CargoController::class);
        // Cualquier usuario autenticado puede ver y editar su propio perfil
        // La policy se encarga de verificar que solo vea/edite lo que le corresponde
        Route::get('usuarios/{usuario}',    [UsuarioController::class, 'show'])->name('usuarios.show');
        Route::put('usuarios/{usuario}',    [UsuarioController::class, 'update'])->name('usuarios.update');
        Route::patch('usuarios/{usuario}',  [UsuarioController::class, 'update'])->name('usuarios.update');

        

        // Solo administradores: CRUD completo de usuarios
        Route::middleware('role:administrador')->group(function () {
            Route::get('usuarios',             [UsuarioController::class, 'index'])->name('usuarios.index');
            Route::post('usuarios',            [UsuarioController::class, 'store'])->name('usuarios.store');
            Route::delete('usuarios/{usuario}',[UsuarioController::class, 'destroy'])->name('usuarios.destroy');
        });

        // Solo administradores: CRUD de roles
        Route::middleware('role:administrador')->group(function () {
            Route::apiResource('roles', RolController::class);
        });
    });
});