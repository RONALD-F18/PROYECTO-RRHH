<?php

namespace Tests\Caracteristicas\Api\Auth;

use App\Models\Usuario;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Tests\TestCase;

class PasswordResetApiTest extends TestCase
{
    public function test_forgot_password_responde_ok_sin_revelar_existencia(): void
    {
        Mail::fake();

        Usuario::factory()->create(['email_usuario' => 'reset@test.local']);

        $this->postJson('/api/v1/forgot-password', [
            'email_usuario' => 'reset@test.local',
        ])
            ->assertOk()
            ->assertJsonPath('success', true);

        $this->postJson('/api/v1/forgot-password', [
            'email_usuario' => 'noexiste@test.local',
        ])
            ->assertOk()
            ->assertJsonPath('success', true);
    }

    public function test_reset_password_con_token_valido(): void
    {
        $email = 'cambio@test.local';
        $tokenPlano = 'token-seguro-de-prueba-64-chars-xxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxx';

        Usuario::factory()->create([
            'email_usuario' => $email,
            'contrasena_usuario' => Hash::make('ClaveAnterior1'),
        ]);

        DB::table('password_reset_tokens')->insert([
            'email_usuario' => $email,
            'token' => hash('sha256', $tokenPlano),
            'expires_at' => now()->addMinutes(30),
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        $this->postJson('/api/v1/reset-password', [
            'email_usuario' => $email,
            'token' => $tokenPlano,
            'contrasena_usuario' => 'NuevaClave1234!',
            'contrasena_usuario_confirmation' => 'NuevaClave1234!',
        ])
            ->assertOk()
            ->assertJsonPath('success', true);

        $this->postJson('/api/v1/login', [
            'email_usuario' => $email,
            'contrasena_usuario' => 'NuevaClave1234!',
        ])->assertOk();
    }

    public function test_reset_password_token_invalido(): void
    {
        Usuario::factory()->create(['email_usuario' => 'invalido@test.local']);

        $tokenInvalido = str_repeat('x', 64);

        $this->postJson('/api/v1/reset-password', [
            'email_usuario' => 'invalido@test.local',
            'token' => $tokenInvalido,
            'contrasena_usuario' => 'NuevaClave1234!',
            'contrasena_usuario_confirmation' => 'NuevaClave1234!',
        ])
            ->assertStatus(422)
            ->assertJsonPath('success', false);
    }
}
