<?php

namespace Tests\Caracteristicas\Api\Riesgo;

use App\Models\Usuario;
use Tests\Soporte\Concerns\ConCabeceraAutenticacionJwt;
use Tests\Soporte\Concerns\ConPruebasModuloApi;
use Tests\TestCase;

class RiesgoApiTest extends TestCase
{
    use ConCabeceraAutenticacionJwt;
    use ConPruebasModuloApi;

    public function test_riesgos_crud_api(): void
    {
        $usuario = Usuario::factory()->create();
        $sufijo = uniqid();

        $this->probarCrudModuloApi(
            $usuario,
            '/api/v1/riesgos',
            [
                'nombre_riesgo' => "Riesgo {$sufijo}",
                'descripcion_riesgo' => 'Clase de riesgo prueba',
            ],
            [
                'nombre_riesgo' => "Riesgo Edit {$sufijo}",
                'descripcion_riesgo' => 'Riesgo actualizado',
            ],
            'cod_riesgo'
        );
    }
}
