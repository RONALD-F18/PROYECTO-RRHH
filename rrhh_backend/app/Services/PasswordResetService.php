<?php

namespace App\Services;

use App\Models\Usuario;
use App\Models\PasswordResetToken;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class PasswordResetService
{
    public function __construct(protected MailService $mailService) {}

    public function sendResetLink(string $email): void
    {
        $usuario = Usuario::where('email_usuario', $email)->first();
        if (!$usuario) return;

        PasswordResetToken::where('email_usuario', $email)->delete();

        $tokenPlano = Str::random(64);

        PasswordResetToken::create([
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
        $registro = PasswordResetToken::where('email_usuario', $email)
            ->where('token', hash('sha256', $token))
            ->first();

        if (!$registro || $registro->isExpired()) return false;

        $usuario = Usuario::where('email_usuario', $email)->first();
        if (!$usuario) return false;

        $usuario->update(['contrasena_usuario' => Hash::make($nuevaContrasena)]);
        $registro->delete();

        return true;
    }
}
