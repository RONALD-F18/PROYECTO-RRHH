<?php

namespace Tests\Caracteristicas\Api\Usuario;

use App\Models\Usuario;
use Illuminate\Support\Facades\Hash;
use Tests\Soporte\Concerns\ConCabeceraAutenticacionJwt;
use Tests\TestCase;

/**
 * Login y logout del flujo de usuarios (JWT + cookie en login).
 */
class AutenticacionUsuarioTest extends TestCase
{
    use ConCabeceraAutenticacionJwt;

    private const claveFabrica = 'ClaveSegura1';

    public function test_login_exitoso_con_credenciales_validas(): void
    {
        $usuario = Usuario::factory()->create([
            'email_usuario' => 'login.ok@prueba.local',
            'contrasena_usuario' => Hash::make(self::claveFabrica),
        ]);

        $respuesta = $this->postJson('/api/v1/login', [
            'email_usuario' => $usuario->email_usuario,
            'contrasena_usuario' => self::claveFabrica,
        ]);

        $respuesta->assertOk()
            ->assertJsonPath('message', 'Acceso Exitoso')
            ->assertJsonPath('role', $usuario->roles->nombre_rol)
            ->assertJsonStructure(['user']);

        $respuesta->assertCookie('token');
    }

    public function test_login_falla_con_contrasena_incorrecta(): void
    {
        $usuario = Usuario::factory()->create([
            'email_usuario' => 'login.fail@prueba.local',
            'contrasena_usuario' => Hash::make(self::claveFabrica),
        ]);

        $this->postJson('/api/v1/login', [
            'email_usuario' => $usuario->email_usuario,
            'contrasena_usuario' => 'OtraClaveIncorrecta9',
        ])->assertUnauthorized()
            ->assertJsonPath('error', 'Credenciales inválidas');
    }

    public function test_logout_responde_ok_con_token_valido(): void
    {
        $usuario = Usuario::factory()->create();

        $this->conJwt($usuario)
            ->postJson('/api/v1/logout')
            ->assertOk()
            ->assertJsonPath('success', true);
    }
}
