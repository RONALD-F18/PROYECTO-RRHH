<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\UsuarioController;
use App\Repositories\Interfaces\RolInterface;
use App\Http\Controllers\RolController;
use App\Http\Controllers\PasswordResetController; // ← agregar este import


Route::prefix('v1')->group(function () {

    // Dentro de Route::prefix('v1'):
    Route::post('/forgot-password', [PasswordResetController::class, 'sendResetLink']);
    Route::post('/reset-password',  [PasswordResetController::class, 'resetPassword']);

    // ─── PÚBLICA ─────────────────────────────────────────────────────────────
    Route::post('/login', [AuthController::class, 'login']);

    // ─── PROTEGIDAS CON JWT ───────────────────────────────────────────────────
    Route::middleware('auth.api')->group(function () {

        Route::post('/logout', [AuthController::class, 'logout']);

        // ─── SOLO ADMINISTRADORES ─────────────────────────────────────────────
        // Módulo de usuarios completamente restringido a admins.
        // Las policies manejan los casos finos (no tocar a otros admins)
        Route::middleware('role:administrador')->group(function () {
            Route::apiResource('usuarios', UsuarioController::class);
        });

        // ─── ADMINISTRADORES Y FUNCIONARIOS ───────────────────────────────────
        // Todo menos el módulo de usuarios.
        // TODO: agregar rutas según módulos que se vayan desarrollando
        Route::middleware('role:administrador,funcionario')->group(function () {
            Route::apiResource('roles', RolController::class);
        });
    });
});