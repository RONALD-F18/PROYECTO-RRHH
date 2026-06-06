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
        if (! $usuario) {
            return;
        }

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

        if (! $registro || $registro->isExpired()) {
            return false;
        }

        $usuario = Usuario::where('email_usuario', $email)->first();
        if (! $usuario) {
            return false;
        }

        $usuario->update(['contrasena_usuario' => Hash::make($nuevaContrasena)]);

        $this->passwordResetRepository->delete($registro);

        return true;
    }

    /**
     * URL del botón en el correo: formulario Laravel en /reset-password.html?token=&email=
     */
    private function buildPasswordResetUrl(string $tokenPlano, string $email): string
    {
        $base = $this->resolvePasswordResetBaseUrl();
        $query = http_build_query([
            'token'  => $tokenPlano,
            'email'  => $email,
            'return' => $this->resolvePasswordResetReturnUrl(),
        ]);

        return str_contains($base, '?') ? ($base.'&'.$query) : ($base.'?'.$query);
    }

    private function resolvePasswordResetBaseUrl(): string
    {
        $configured = rtrim((string) config('rrhh.password_reset_url'), '/');
        if ($configured !== '') {
            return $configured;
        }

        $appUrl = rtrim((string) config('app.url', env('APP_URL', 'http://localhost')), '/');
        $path = (string) config('rrhh.password_reset_path', '/reset-password.html');
        if (! str_starts_with($path, '/')) {
            $path = '/'.$path;
        }

        return $appUrl.$path;
    }

    private function resolvePasswordResetReturnUrl(): string
    {
        $return = rtrim((string) config('rrhh.password_reset_return_url'), '/');
        if ($return !== '') {
            return $return;
        }

        return rtrim((string) env('FRONTEND_URL', config('app.url')), '/');
    }
}
