<?php

namespace App\Http\Controllers;

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
            $result = $this->authService->login(credentials: $credentials);
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
            $invalidCredentials = 'Credenciales inválidas';
            $statusCode = $e->getMessage() === $invalidCredentials ? 401 : 500;
            $errorMessage = $e->getMessage() === $invalidCredentials
                ? $invalidCredentials
                : 'Token no generado, error interno del servidor';

            return response()->json([
                'error' => $errorMessage,
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
