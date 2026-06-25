<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Illuminate\Auth\AuthenticationException;
use Illuminate\Database\QueryException;
use App\Http\Middleware\AuthenticateApi;
use App\Http\Middleware\RoleMiddleware;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        api: __DIR__.'/../routes/api.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware) {
        $middleware->alias([
            'auth.api' => AuthenticateApi::class,
            'role' => RoleMiddleware::class,
        ]);
    })
    ->withExceptions(function (Exceptions $exceptions) {
        $exceptions->render(function (AuthenticationException $e, $request) {
            if ($request->is('api/*') || $request->expectsJson()) {
                return response()->json([
                    'message' => $e->getMessage() ?: 'No autenticado.',
                ], 401);
            }

            return null;
        });

        $exceptions->render(function (QueryException $e, $request) {
            if (! $request->is('api/*') && ! $request->expectsJson()) {
                return null;
            }

            $sqlState = $e->errorInfo[1] ?? null;

            $mensaje = match ($sqlState) {
                1062 => 'Ya existe un registro con esos datos (documento, correo, teléfono o cuenta duplicados).',
                1048 => 'Faltan datos obligatorios para guardar el registro.',
                1452 => 'Una referencia no es válida (banco, usuario u otro catálogo inexistente).',
                default => 'No se pudo guardar el registro. Verifique los datos enviados.',
            };

            return response()->json([
                'message' => $mensaje,
                'errors' => ['general' => [$mensaje]],
            ], 422);
        });
    })
    ->create();