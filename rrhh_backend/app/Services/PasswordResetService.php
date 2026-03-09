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

        $resetUrl = 'https://literate-zebra-x5vpxjw654pq267jv-8080.app.github.dev/reset-password.html'
            . '?token=' . $tokenPlano
            . '&email=' . urlencode($email);

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
}