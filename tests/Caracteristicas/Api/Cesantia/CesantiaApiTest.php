<?php

namespace Tests\Caracteristicas\Api\Cesantia;

use App\Models\Usuario;
use Tests\Soporte\Concerns\ConCabeceraAutenticacionJwt;
use Tests\Soporte\Concerns\ConPruebasModuloApi;
use Tests\TestCase;

class CesantiaApiTest extends TestCase
{
    use ConCabeceraAutenticacionJwt;
    use ConPruebasModuloApi;

    public function test_cesantias_crud_api(): void
    {
        $usuario = Usuario::factory()->create();
        $sufijo = uniqid();

        $this->probarCrudModuloApi(
            $usuario,
            '/api/v1/cesantias',
            [
                'nombre_fondo_cesantia' => "Cesantia {$sufijo}",
                'descripcion_fondo_cesantia' => 'Fondo cesantias prueba',
            ],
            [
                'nombre_fondo_cesantia' => "Cesantia Edit {$sufijo}",
                'descripcion_fondo_cesantia' => 'Fondo actualizado',
            ],
            'cod_fondo_cesantias'
        );
    }
}
