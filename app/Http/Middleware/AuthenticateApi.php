<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Auth\AuthenticationException;
use Illuminate\Http\Request;

/**
 * Compatibilidad con alias auth.api: misma validación que Sanctum Bearer.
 */
class AuthenticateApi
{
    public function handle(Request $request, Closure $next, ...$guards)
    {
        if (! $request->user()) {
            throw new AuthenticationException('No autenticado, Inicia Sesión.');
        }

        return $next($request);
    }
}
