<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Services\AuthService;
use App\Http\Requests\AuthRequest as LoginRequest;

class AuthController extends Controller
{
    protected $authService;

 
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

            $token = $result['token'];
        
            return response()->json([
                'message' => 'Acceso Exitoso',

                'role' => $user->roles->nombre_rol ?? null,

                'user' => $user
            ])

            ->cookie('token', $token, 60 * 24, null, null, false, true);

        } catch (\Exception $e) {

            $invalidCredentials = 'Credenciales inválidas';


            $statusCode = $e->getMessage() === $invalidCredentials ? 401 : 500;

            $errorMessage = $e->getMessage() === $invalidCredentials
                ? $invalidCredentials
                : 'Token no generado, error interno del servidor';

            return response()->json([
                'error' => $errorMessage
            ], $statusCode);
        }
    }
    
    
		public function logout()
    {
        return response()->json([
            'success' => true,
            'message' => 'Sesión cerrada correctamente'
        ], 200)->cookie('token', '', -1);
    }
}
