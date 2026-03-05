<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\UsuarioController;
use App\Http\Controllers\RolController;
use App\Http\Controllers\BancoController;
use App\Http\Controllers\EmpleadoController;


// Todas las rutas que tengan el prefijo /v1
Route::prefix('v1')->group(function () {
    // Login
   
   
    Route::post('/login', action: [AuthController::class, 'login']); //todos los usuarios pueden acceder a esta ruta para iniciar sesión
    Route::apiResource('bancos', BancoController::class);
    Route::apiResource('usuarios', UsuarioController::class); 
    Route::apiResource('empleados', EmpleadoController::class);



    // Rutas protegidas con JWT
    Route::middleware('auth.api')->group(function () {

        Route::post('/logout', [AuthController::class, 'logout']);

        Route::middleware('role:administrador')->group(function () {
            // Rutas para administración de usuarios
            
        });

        Route::middleware('role:editor, administrador')->group(function () {
            Route::apiResource('/roles', RolController::class);
        });
    });
});
