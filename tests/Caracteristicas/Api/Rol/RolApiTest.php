<?php

namespace Tests\Caracteristicas\Api\Rol;

use App\Models\Usuario;
use Tests\Soporte\Concerns\ConCabeceraAutenticacionJwt;
use Tests\Soporte\Concerns\ConPruebasModuloApi;
use Tests\TestCase;

class RolApiTest extends TestCase
{
    use ConCabeceraAutenticacionJwt;
    use ConPruebasModuloApi;

    public function test_funcionario_no_puede_gestionar_roles(): void
    {
        $funcionario = Usuario::factory()->create();

        $this->getJson('/api/v1/roles')->assertUnauthorized();

        $this->conJwt($funcionario)
            ->getJson('/api/v1/roles')
            ->assertForbidden();
    }

    public function test_administrador_crud_roles(): void
    {
        $admin = Usuario::factory()->administrador()->create();
        $sufijo = uniqid();

        $this->probarCrudModuloApi(
            $admin,
            '/api/v1/roles',
            [
                'nombre_rol' => "rol_test_{$sufijo}",
                'estado_rol' => true,
                'descripcion' => 'Rol de prueba',
            ],
            [
                'nombre_rol' => "rol_edit_{$sufijo}",
                'descripcion' => 'Rol actualizado',
            ],
            'cod_rol'
        );
    }
}
