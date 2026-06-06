<?php

namespace Tests\Caracteristicas\Api\Arl;

use App\Models\Usuario;
use Tests\Soporte\Concerns\ConCabeceraAutenticacionJwt;
use Tests\Soporte\Concerns\ConPruebasModuloApi;
use Tests\TestCase;

class ArlApiTest extends TestCase
{
    use ConCabeceraAutenticacionJwt;
    use ConPruebasModuloApi;

    public function test_arls_crud_api(): void
    {
        $usuario = Usuario::factory()->create();
        $sufijo = uniqid();

        $this->probarCrudModuloApi(
            $usuario,
            '/api/v1/arls',
            [
                'nombre_arl' => "ARL {$sufijo}",
                'descripcion_arl' => 'Descripcion ARL',
            ],
            [
                'nombre_arl' => "ARL Edit {$sufijo}",
                'descripcion_arl' => 'ARL actualizada',
            ],
            'cod_arl'
        );
    }
}
