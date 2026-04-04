<?php

namespace Tests\Unidad\Politicas;

use App\Models\Usuario;
use App\Policies\UserPolicy;
use Tests\TestCase;

/**
 * Pruebas directas de UserPolicy (sin HTTP).
 */
class PoliticaUsuarioTest extends TestCase
{
    private UserPolicy $politica;

    protected function setUp(): void
    {
        parent::setUp();
        $this->politica = new UserPolicy;
    }

    public function test_usuario_puede_actualizar_su_propia_cuenta(): void
    {
        $usuario = Usuario::factory()->create();

        $this->assertTrue(
            $this->politica->update($usuario, $usuario),
            'La policy debe permitir editar la propia cuenta.'
        );
    }

    public function test_funcionario_no_puede_actualizar_a_otro_funcionario(): void
    {
        $autenticado = Usuario::factory()->create();
        $otro = Usuario::factory()->create();

        $this->assertFalse(
            $this->politica->update($autenticado, $otro),
            'Un funcionario no debe poder editar a otro usuario.'
        );
    }

    public function test_administrador_puede_actualizar_funcionario(): void
    {
        $admin = Usuario::factory()->administrador()->create();
        $funcionario = Usuario::factory()->create();

        $this->assertTrue(
            $this->politica->update($admin, $funcionario),
            'El administrador debe poder editar funcionarios.'
        );
    }

    public function test_administrador_no_puede_actualizar_a_otro_administrador(): void
    {
        $adminA = Usuario::factory()->administrador()->create();
        $adminB = Usuario::factory()->administrador()->create();

        $this->assertFalse(
            $this->politica->update($adminA, $adminB),
            'Un administrador no debe poder editar a otro administrador por API.'
        );
    }

    public function test_ver_perfil_propio_siempre_permitido(): void
    {
        $usuario = Usuario::factory()->create();

        $this->assertTrue($this->politica->view($usuario, $usuario));
    }

    public function test_funcionario_no_puede_ver_a_otro(): void
    {
        $a = Usuario::factory()->create();
        $b = Usuario::factory()->create();

        $this->assertFalse($this->politica->view($a, $b));
    }

    public function test_administrador_puede_ver_funcionario(): void
    {
        $admin = Usuario::factory()->administrador()->create();
        $funcionario = Usuario::factory()->create();

        $this->assertTrue($this->politica->view($admin, $funcionario));
    }

    public function test_administrador_no_puede_ver_otro_administrador(): void
    {
        $adminA = Usuario::factory()->administrador()->create();
        $adminB = Usuario::factory()->administrador()->create();

        $this->assertFalse($this->politica->view($adminA, $adminB));
    }

    public function test_crear_usuario_solo_admin_y_nunca_rol_administrador(): void
    {
        $admin = Usuario::factory()->administrador()->create();
        $funcionario = Usuario::factory()->create();

        $this->assertTrue($this->politica->create($admin, 'funcionario'));
        $this->assertFalse($this->politica->create($admin, 'administrador'));
        $this->assertFalse($this->politica->create($funcionario, 'funcionario'));
    }

    public function test_eliminar_solo_admin_y_nunca_a_administrador(): void
    {
        $admin = Usuario::factory()->administrador()->create();
        $funcionario = Usuario::factory()->create();
        $otroFuncionario = Usuario::factory()->create();
        $otroAdmin = Usuario::factory()->administrador()->create();

        $this->assertTrue($this->politica->delete($admin, $funcionario));
        $this->assertFalse($this->politica->delete($admin, $otroAdmin));
        $this->assertFalse($this->politica->delete($funcionario, $otroFuncionario));
    }
}
