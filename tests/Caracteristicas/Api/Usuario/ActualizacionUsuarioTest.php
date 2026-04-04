<?php

namespace Tests\Caracteristicas\Api\Usuario;

use App\Models\Rol;
use App\Models\Usuario;
use Tests\Soporte\Concerns\ConCabeceraAutenticacionJwt;
use Tests\TestCase;

/**
 * PUT/PATCH usuarios/{id} — policy, validación y reglas de rol.
 */
class ActualizacionUsuarioTest extends TestCase
{
    use ConCabeceraAutenticacionJwt;

    public function test_funcionario_puede_actualizar_su_correo_con_patch(): void
    {
        $usuario = Usuario::factory()->create(['email_usuario' => 'antes@prueba.local']);
        $correoNuevo = 'despues@prueba.local';

        $this->conJwt($usuario)
            ->patchJson(route('usuarios.update', ['usuario' => $usuario->cod_usuario]), [
                'email_usuario' => $correoNuevo,
            ])
            ->assertOk()
            ->assertJsonPath('success', true);

        $this->assertSame($correoNuevo, $usuario->fresh()->email_usuario);
    }

    public function test_funcionario_no_puede_modificar_su_propio_rol(): void
    {
        $usuario = Usuario::factory()->create();
        $codRolAdministrador = Rol::query()->where('nombre_rol', 'administrador')->value('cod_rol');

        $this->conJwt($usuario)
            ->patchJson(route('usuarios.update', ['usuario' => $usuario->cod_usuario]), [
                'cod_rol' => $codRolAdministrador,
            ])
            ->assertForbidden()
            ->assertJsonPath('message', 'No puedes modificar tu propio rol.');
    }

    public function test_funcionario_no_puede_actualizar_otro_usuario(): void
    {
        $autenticado = Usuario::factory()->create();
        $otro = Usuario::factory()->create();

        $this->conJwt($autenticado)
            ->patchJson(route('usuarios.update', ['usuario' => $otro->cod_usuario]), [
                'nombre_usuario' => 'hack_intento',
            ])
            ->assertForbidden();
    }

    public function test_administrador_puede_actualizar_datos_de_funcionario(): void
    {
        $admin = Usuario::factory()->administrador()->create();
        $funcionario = Usuario::factory()->create(['nombre_usuario' => 'nombre_viejo']);

        $this->conJwt($admin)
            ->patchJson(route('usuarios.update', ['usuario' => $funcionario->cod_usuario]), [
                'nombre_usuario' => 'nombre_nuevo_func',
            ])
            ->assertOk()
            ->assertJsonPath('success', true);

        $this->assertSame('nombre_nuevo_func', $funcionario->fresh()->nombre_usuario);
    }

    public function test_administrador_no_puede_asignar_rol_administrador_por_api(): void
    {
        $admin = Usuario::factory()->administrador()->create();
        $funcionario = Usuario::factory()->create();
        $codRolAdministrador = Rol::query()->where('nombre_rol', 'administrador')->value('cod_rol');

        $this->conJwt($admin)
            ->patchJson(route('usuarios.update', ['usuario' => $funcionario->cod_usuario]), [
                'cod_rol' => $codRolAdministrador,
            ])
            ->assertForbidden()
            ->assertJsonPath('message', 'No se puede asignar el rol administrador por la API.');
    }

    public function test_administrador_no_puede_actualizar_otro_administrador(): void
    {
        $adminA = Usuario::factory()->administrador()->create();
        $adminB = Usuario::factory()->administrador()->create();

        $this->conJwt($adminA)
            ->patchJson(route('usuarios.update', ['usuario' => $adminB->cod_usuario]), [
                'nombre_usuario' => 'cambio_no_permitido',
            ])
            ->assertForbidden();
    }

    public function test_sin_token_recibe_401(): void
    {
        $usuario = Usuario::factory()->create();

        $this->patchJson(route('usuarios.update', ['usuario' => $usuario->cod_usuario]), [
            'email_usuario' => 'x@y.local',
        ])->assertUnauthorized();
    }
}
