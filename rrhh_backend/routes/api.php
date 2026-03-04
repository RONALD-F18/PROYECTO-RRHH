<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\UsuarioController;
use App\Http\Controllers\RolController;

Route::prefix('v1')->group(function () {

    // ─── PÚBLICA ─────────────────────────────────────────────────────────────
    Route::post('/login', [AuthController::class, 'login']);

    // ─── PROTEGIDAS CON JWT ───────────────────────────────────────────────────
    Route::middleware('auth.api')->group(function () {

        Route::post('/logout', [AuthController::class, 'logout']);

        // ─── SOLO ADMINISTRADORES ─────────────────────────────────────────────
        // Módulo de usuarios y roles completamente restringido a admins.
        // Las policies dentro del controller manejan los casos finos
        // (no ver/editar/eliminar a otros admins)
        Route::middleware('role:administrador')->group(function () {
            Route::apiResource('usuarios', UsuarioController::class);
            Route::apiResource('roles', RolController::class);
        });

        // ─── ADMINISTRADORES Y FUNCIONARIOS ───────────────────────────────────
        // Rutas compartidas, todo menos el módulo de usuarios.
        // TODO: agregar rutas según módulos que se vayan desarrollando
        Route::middleware('role:administrador,funcionario')->group(function () {
            // Ejemplo: Route::apiResource('nomina', NominaController::class);
        });
    });
});