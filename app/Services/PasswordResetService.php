<?php

namespace App\Services;

use App\Models\Usuario;
use App\Repositories\Interfaces\PasswordResetInterface;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class PasswordResetService
{
    public function __construct(
        protected MailService $mailService,
        protected PasswordResetInterface $passwordResetRepository
    ) {}

    public function sendResetLink(string $email): void
    {
        $usuario = Usuario::where('email_usuario', $email)->first();
        if (!$usuario) return;

        $this->passwordResetRepository->deleteByEmail($email);

        $tokenPlano = Str::random(64);

        $this->passwordResetRepository->create([
            'email_usuario' => $email,
            'token'         => hash('sha256', $tokenPlano),
            'expires_at'    => now()->addMinutes(30),
        ]);

        $resetUrl = $this->buildPasswordResetUrl($tokenPlano, $email);

        $this->mailService->sendPasswordReset(
            $email,
            $usuario->nombre_usuario,
            $resetUrl
        );
    }

    public function resetPassword(string $email, string $token, string $nuevaContrasena): bool
    {
        $registro = $this->passwordResetRepository->findByEmailAndToken(
            $email,
            hash('sha256', $token)
        );

        if (!$registro || $registro->isExpired()) return false;

        $usuario = Usuario::where('email_usuario', $email)->first();
        if (!$usuario) return false;

        $usuario->update(['contrasena_usuario' => Hash::make($nuevaContrasena)]);

        $this->passwordResetRepository->delete($registro);

        return true;
    }

    /**
     * URL del botón en el correo (Blade usa $resetUrl tal cual).
     * Corrige enlaces viejos que apuntaban a #/recuperar-contrasena (solo "olvidé mi clave").
     */
    private function buildPasswordResetUrl(string $tokenPlano, string $email): string
    {
        $base = $this->resolvePasswordResetBaseUrl();
        $separator = str_contains($base, '?') ? '&' : '?';

        return $base . $separator . 'token=' . urlencode($tokenPlano) . '&email=' . urlencode($email);
    }

    private function resolvePasswordResetBaseUrl(): string
    {
        $base = rtrim((string) config('rrhh.password_reset_url'), '/');

        if ($base === '') {
            $frontend = rtrim((string) env('FRONTEND_URL', 'https://ronald-f18.github.io'), '/');
            $path = (string) config('rrhh.password_reset_path', '/PROYECTO-REACT-RRHH/#/cambiar-contrasena');
            if (! str_starts_with($path, '/')) {
                $path = '/' . $path;
            }
            $base = $frontend . $path;
        }

        // Producción/Azure quedó con recuperar-contrasena; el correo debe ir al formulario con token.
        if (str_contains($base, '/recuperar-contrasena')) {
            $base = str_replace('/recuperar-contrasena', '/cambiar-contrasena', $base);
        }

        return $base;
    }
}