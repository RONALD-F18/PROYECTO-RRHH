<?php

namespace Tests\Caracteristicas\Api\Compensacion;

use App\Models\Usuario;
use Tests\Soporte\Concerns\ConCabeceraAutenticacionJwt;
use Tests\Soporte\Concerns\ConPruebasModuloApi;
use Tests\TestCase;

class CompensacionApiTest extends TestCase
{
    use ConCabeceraAutenticacionJwt;
    use ConPruebasModuloApi;

    public function test_compensaciones_crud_api(): void
    {
        $usuario = Usuario::factory()->create();
        $sufijo = uniqid();

        $this->probarCrudModuloApi(
            $usuario,
            '/api/v1/compensaciones',
            [
                'nombre' => "Caja {$sufijo}",
                'descripcion_caja_compensacion' => 'Caja compensacion prueba',
            ],
            [
                'nombre' => "Caja Edit {$sufijo}",
                'descripcion_caja_compensacion' => 'Caja actualizada',
            ],
            'cod_caja_compensacion'
        );
    }
}
