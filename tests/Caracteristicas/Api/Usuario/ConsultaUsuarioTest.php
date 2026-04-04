<?php

namespace Tests\Caracteristicas\Api\Usuario;

use App\Models\Usuario;
use Tests\Soporte\Concerns\ConCabeceraAutenticacionJwt;
use Tests\TestCase;

/**
 * GET usuarios/{id} — policy de visualización.
 */
class ConsultaUsuarioTest extends TestCase
{
    use ConCabeceraAutenticacionJwt;

    public function test_funcionario_ve_su_propio_perfil(): void
    {
        $usuario = Usuario::factory()->create();

        $this->conJwt($usuario)
            ->getJson(route('usuarios.show', ['usuario' => $usuario->cod_usuario]))
            ->assertOk()
            ->assertJsonPath('success', true)
            ->assertJsonPath('data.cod_usuario', $usuario->cod_usuario);
    }

    public function test_funcionario_no_puede_ver_perfil_de_otro(): void
    {
        $autenticado = Usuario::factory()->create();
        $otro = Usuario::factory()->create();

        $this->conJwt($autenticado)
            ->getJson(route('usuarios.show', ['usuario' => $otro->cod_usuario]))
            ->assertForbidden()
            ->assertJsonPath('success', false);
    }

    public function test_administrador_puede_ver_funcionario(): void
    {
        $admin = Usuario::factory()->administrador()->create();
        $funcionario = Usuario::factory()->create();

        $this->conJwt($admin)
            ->getJson(route('usuarios.show', ['usuario' => $funcionario->cod_usuario]))
            ->assertOk()
            ->assertJsonPath('data.cod_usuario', $funcionario->cod_usuario);
    }

    public function test_administrador_no_puede_ver_otro_administrador(): void
    {
        $adminA = Usuario::factory()->administrador()->create();
        $adminB = Usuario::factory()->administrador()->create();

        $this->conJwt($adminA)
            ->getJson(route('usuarios.show', ['usuario' => $adminB->cod_usuario]))
            ->assertForbidden();
    }

    public function test_usuario_inexistente_devuelve_404(): void
    {
        $usuario = Usuario::factory()->create();

        $this->conJwt($usuario)
            ->getJson(route('usuarios.show', ['usuario' => 999_999]))
            ->assertNotFound()
            ->assertJsonPath('success', false);
    }

    public function test_sin_token_no_puede_consultar_perfil(): void
    {
        $usuario = Usuario::factory()->create();

        $this->getJson(route('usuarios.show', ['usuario' => $usuario->cod_usuario]))
            ->assertUnauthorized();
    }
}
