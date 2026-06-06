<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Log;
use App\Http\Requests\AuthRequest as LoginRequest;
use App\Services\AuthService;
use Illuminate\Http\Request;

class AuthController extends Controller
{
    protected AuthService $authService;

    public function __construct(AuthService $authService)
    {
        $this->authService = $authService;
    }

   public function login(LoginRequest $login)
{
    $credentials = $login->only('email_usuario', 'contrasena_usuario');

    try {

        Log::info('Intentando login', [
            'email_usuario' => $credentials['email_usuario'] ?? null,
        ]);

        $result = $this->authService->login(credentials: $credentials);

        Log::info('Login exitoso');

        $user = $result['user'];

        return response()->json([
            'message' => 'Acceso Exitoso',
            'role' => $user->roles->nombre_rol ?? null,
            'user' => $user,
            'access_token' => $result['access_token'],
            'token' => $result['token'],
            'token_type' => $result['token_type'],
        ]);

    } catch (\Exception $e) {

        Log::error('ERROR EN LOGIN JWT', [
            'message' => $e->getMessage(),
            'line' => $e->getLine(),
            'file' => $e->getFile(),
            'trace' => $e->getTraceAsString(),
        ]);

        $invalidCredentials = 'Credenciales inválidas';

        $statusCode = $e->getMessage() === $invalidCredentials
            ? 401
            : 500;

        return response()->json([
            'error' => $e->getMessage(),
            'debug' => [
                'line' => $e->getLine(),
                'file' => $e->getFile(),
            ]
        ], $statusCode);
    }
}

    public function logout(Request $request)
    {
        $request->user()?->currentAccessToken()?->delete();

        return response()->json([
            'success' => true,
            'message' => 'Sesión cerrada correctamente',
        ], 200);
    }
}
