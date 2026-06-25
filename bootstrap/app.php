<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Illuminate\Auth\AuthenticationException;
use Illuminate\Database\QueryException;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\ValidationException;
use App\Http\Middleware\AuthenticateApi;
use App\Http\Middleware\RoleMiddleware;
use Throwable;

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

            Log::error('QueryException en API', [
                'url' => $request->fullUrl(),
                'sql' => $e->getSql(),
                'message' => $e->getMessage(),
            ]);

            $sqlState = $e->errorInfo[1] ?? null;
            $raw = $e->getMessage();

            if (str_contains($raw, "Unknown column 'sexo'")) {
                $mensaje = 'Falta la columna sexo en empleados. Ejecute en el servidor: php artisan migrate --force';
            } else {
                $mensaje = match ($sqlState) {
                    1062 => 'Ya existe un registro con esos datos (documento, correo, teléfono o cuenta duplicados).',
                    1048 => 'Faltan datos obligatorios para guardar el registro.',
                    1452 => 'Una referencia no es válida (banco, usuario u otro catálogo inexistente).',
                    default => 'No se pudo guardar el registro. Verifique los datos enviados.',
                };
            }

            return response()->json([
                'message' => $mensaje,
                'errors' => ['general' => [$mensaje]],
            ], 422);
        });

        $exceptions->render(function (ValidationException $e, $request) {
            if (! $request->is('api/*') && ! $request->expectsJson()) {
                return null;
            }

            return response()->json([
                'message' => $e->getMessage() ?: 'Los datos enviados no son válidos.',
                'errors' => $e->errors(),
            ], $e->status);
        });

        $exceptions->render(function (Throwable $e, $request) {
            if (! $request->is('api/*') && ! $request->expectsJson()) {
                return null;
            }

            if ($e instanceof AuthenticationException
                || $e instanceof ValidationException
                || $e instanceof QueryException) {
                return null;
            }

            Log::error('Excepción no controlada en API', [
                'url' => $request->fullUrl(),
                'message' => $e->getMessage(),
                'file' => $e->getFile(),
                'line' => $e->getLine(),
            ]);

            $mensaje = config('app.debug')
                ? $e->getMessage()
                : 'El sistema tuvo un fallo temporal. Intente más tarde o consulte al administrador.';

            return response()->json([
                'message' => $mensaje,
                'errors' => ['general' => [$mensaje]],
            ], 500);
        });
    })
    ->create();