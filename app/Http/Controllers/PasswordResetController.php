<?php

namespace App\Http\Controllers;

use App\Services\PasswordResetService;
use App\Http\Requests\ForgotPasswordRequest;
use App\Http\Requests\ResetPasswordRequest;

class PasswordResetController extends Controller
{

    public function __construct(protected PasswordResetService $passwordResetService) {}

    /**
     * POST /v1/forgot-password
     * Siempre responde 200 — no revelamos si el email existe
     */
    public function sendResetLink(ForgotPasswordRequest $request)
    {
        $this->passwordResetService->sendResetLink($request->email_usuario);

        return response()->json([
            'success' => true,
            'message' => 'Si el correo está registrado recibirás el token en tu bandeja de entrada.',
        ], 200);
    }

    /**
     * POST /v1/reset-password
     * El frontend manda: email, token (del correo) y nueva contraseña
     */
    public function resetPassword(ResetPasswordRequest $request)
    {
        $ok = $this->passwordResetService->resetPassword(
            $request->email_usuario,
            $request->token,
            $request->contrasena_usuario
        );

        if (!$ok) {
            return response()->json([
                'success' => false,
                'message' => 'Token inválido o expirado. Solicita uno nuevo.',
            ], 422);
        }

        return response()->json([
            'success' => true,
            'message' => 'Contraseña actualizada correctamente.',
        ], 200);
    }
}