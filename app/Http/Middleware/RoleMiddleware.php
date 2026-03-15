<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class RoleMiddleware
{
    public function handle(Request $request, Closure $next, ...$roles)
{
    $user = $request->user('api');

    $userRole = $user->roles->nombre_rol; 
    
    if (!in_array($userRole, $roles)) {
        return response()->json([
            'success' => false,
            'message' => 'No tienes permisos para acceder',
        ], 403);
    }

    return $next($request);
}
}