<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Auth\AuthenticationException;
class AuthenticateApi
{
    public function handle($request, Closure $next, ...$guards)
    {
        if (!auth()->guard('api')->check()) {
            throw new AuthenticationException('No autenticado, Inicia Sesión.');
        }

        return $next($request);
    }
}
