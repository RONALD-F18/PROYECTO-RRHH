<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\UsuarioController;
use App\Http\Controllers\RolController;
use App\Http\Controllers\EpsController;
use App\Http\Controllers\RiesgoController;
use App\Http\Controllers\ArlController;
use App\Http\Controllers\PensionController;
use App\Http\Controllers\CesantiaController;
use App\Http\Controllers\CompensacionController;
use App\Http\Controllers\AfiliacionController;



// Todas las rutas que tengan el prefijo /v1
Route::prefix('v1')->group(function () {
    // Login
    Route::post('/login', action: [AuthController::class, 'login']); //todos los usuarios pueden acceder a esta ruta para iniciar sesión

    Route::apiResource('usuarios', UsuarioController::class); 
    Route::apiResource('eps', EpsController::class);
    Route::apiResource('riesgos', RiesgoController::class);
    Route::apiResource('arls', ArlController::class);
    Route::apiResource('pensiones', PensionController::class);
    Route::apiResource('cesantias', CesantiaController::class);
    Route::apiResource('compensaciones', CompensacionController::class);
    Route::apiResource('afiliaciones', AfiliacionController::class);
     
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