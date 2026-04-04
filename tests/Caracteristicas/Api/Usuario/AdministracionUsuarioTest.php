<?php

namespace Tests\Caracteristicas\Api\Usuario;

use App\Models\Rol;
use App\Models\Usuario;
use Illuminate\Support\Facades\Hash;
use Tests\Soporte\Concerns\ConCabeceraAutenticacionJwt;
use Tests\TestCase;

/**
 * Listado, alta y baja (solo rol administrador).
 */
class AdministracionUsuarioTest extends TestCase
{
    use ConCabeceraAutenticacionJwt;

    public function test_funcionario_no_puede_listar_usuarios(): void
    {
        $funcionario = Usuario::factory()->create();

        $this->conJwt($funcionario)
            ->getJson(route('usuarios.index'))
            ->assertForbidden()
            ->assertJsonPath('message', 'No tienes permisos para acceder');
    }

    public function test_administrador_lista_usuarios_y_enmascara_otro_admin(): void
    {
        $adminPrincipal = Usuario::factory()->administrador()->create();
        $otroAdmin = Usuario::factory()->administrador()->create();
        Usuario::factory()->create();

        $respuesta = $this->conJwt($adminPrincipal)->getJson(route('usuarios.index'));

        $respuesta->assertOk()->assertJsonPath('success', true);

        $datos = collect($respuesta->json('data'));
        $filaOtroAdmin = $datos->firstWhere('cod_usuario', $otroAdmin->cod_usuario);

        $this->assertIsArray($filaOtroAdmin);
        $this->assertSame('Información restringida', $filaOtroAdmin['detalle'] ?? null);
        $this->assertArrayHasKey('nombre', $filaOtroAdmin);
        $this->assertArrayNotHasKey('email_usuario', $filaOtroAdmin);
    }

    public function test_administrador_crea_funcionario_con_datos_validos(): void
    {
        $admin = Usuario::factory()->administrador()->create();
        $codRolFuncionario = Rol::query()->where('nombre_rol', 'funcionario')->value('cod_rol');

        $carga = [
            'nombre_usuario' => 'nuevo_funcionario_test',
            'email_usuario' => 'nuevo.func@prueba.local',
            'contrasena_usuario' => 'PasswordLarga1',
            'cod_rol' => $codRolFuncionario,
            'estado_usuario' => true,
        ];

        $respuesta = $this->conJwt($admin)->postJson(route('usuarios.store'), $carga);

        $respuesta->assertCreated()
            ->assertJsonPath('success', true);

        $this->assertDatabaseHas('usuarios', [
            'nombre_usuario' => 'nuevo_funcionario_test',
            'email_usuario' => 'nuevo.func@prueba.local',
            'cod_rol' => $codRolFuncionario,
        ]);

        $creado = Usuario::query()->where('email_usuario', 'nuevo.func@prueba.local')->first();
        $this->assertTrue(Hash::check('PasswordLarga1', $creado->contrasena_usuario));
    }

    public function test_administrador_no_puede_crear_otro_administrador_por_api(): void
    {
        $admin = Usuario::factory()->administrador()->create();
        $codRolAdmin = Rol::query()->where('nombre_rol', 'administrador')->value('cod_rol');

        $this->conJwt($admin)->postJson(route('usuarios.store'), [
            'nombre_usuario' => 'admin_nuevo_mal',
            'email_usuario' => 'admin.mal@prueba.local',
            'contrasena_usuario' => 'PasswordLarga1',
            'cod_rol' => $codRolAdmin,
            'estado_usuario' => true,
        ])->assertForbidden()
            ->assertJsonPath('message', 'Los administradores no se crean por API, usa el seeder');
    }

    public function test_funcionario_no_puede_crear_usuario(): void
    {
        $funcionario = Usuario::factory()->create();
        $codRolFuncionario = Rol::query()->where('nombre_rol', 'funcionario')->value('cod_rol');

        $this->conJwt($funcionario)->postJson(route('usuarios.store'), [
            'nombre_usuario' => 'no_debe_existir',
            'email_usuario' => 'no.debe@prueba.local',
            'contrasena_usuario' => 'PasswordLarga1',
            'cod_rol' => $codRolFuncionario,
            'estado_usuario' => true,
        ])->assertForbidden();
    }

    public function test_administrador_elimina_funcionario(): void
    {
        $admin = Usuario::factory()->administrador()->create();
        $victima = Usuario::factory()->create();

        $this->conJwt($admin)
            ->deleteJson(route('usuarios.destroy', ['usuario' => $victima->cod_usuario]))
            ->assertOk()
            ->assertJsonPath('success', true);

        $this->assertDatabaseMissing('usuarios', ['cod_usuario' => $victima->cod_usuario]);
    }

    public function test_administrador_no_puede_eliminar_otro_administrador(): void
    {
        $admin = Usuario::factory()->administrador()->create();
        $otroAdmin = Usuario::factory()->administrador()->create();

        $this->conJwt($admin)
            ->deleteJson(route('usuarios.destroy', ['usuario' => $otroAdmin->cod_usuario]))
            ->assertForbidden();
    }

    public function test_funcionario_no_puede_eliminar_usuario(): void
    {
        $funcionario = Usuario::factory()->create();
        $otro = Usuario::factory()->create();

        $this->conJwt($funcionario)
            ->deleteJson(route('usuarios.destroy', ['usuario' => $otro->cod_usuario]))
            ->assertForbidden();
    }
}
