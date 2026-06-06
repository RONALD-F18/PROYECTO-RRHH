<?php

namespace Tests\Caracteristicas\Api\Eps;

use App\Models\Usuario;
use Tests\Soporte\Concerns\ConCabeceraAutenticacionJwt;
use Tests\Soporte\Concerns\ConPruebasModuloApi;
use Tests\TestCase;

class EpsApiTest extends TestCase
{
    use ConCabeceraAutenticacionJwt;
    use ConPruebasModuloApi;

    public function test_eps_crud_api(): void
    {
        $usuario = Usuario::factory()->create();
        $sufijo = uniqid();

        $this->probarCrudModuloApi(
            $usuario,
            '/api/v1/eps',
            [
                'nombre_eps' => "EPS {$sufijo}",
                'descripcion_eps' => 'Descripcion EPS',
            ],
            [
                'nombre_eps' => "EPS Edit {$sufijo}",
                'descripcion_eps' => 'EPS actualizada',
            ],
            'cod_eps'
        );
    }
}
