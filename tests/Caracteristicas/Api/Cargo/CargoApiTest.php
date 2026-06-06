<?php

namespace Tests\Caracteristicas\Api\Cargo;

use App\Models\Usuario;
use Tests\Soporte\Concerns\ConCabeceraAutenticacionJwt;
use Tests\Soporte\Concerns\ConPruebasModuloApi;
use Tests\TestCase;

class CargoApiTest extends TestCase
{
    use ConCabeceraAutenticacionJwt;
    use ConPruebasModuloApi;

    public function test_cargos_crud_api(): void
    {
        $usuario = Usuario::factory()->create();
        $sufijo = uniqid();

        $this->probarCrudModuloApi(
            $usuario,
            '/api/v1/cargos',
            [
                'nomb_cargo' => "Cargo API {$sufijo}",
                'descripcion' => 'Cargo inicial',
            ],
            [
                'nomb_cargo' => "Cargo API Edit {$sufijo}",
                'descripcion' => 'Cargo actualizado',
            ],
            'cod_cargo'
        );
    }
}
