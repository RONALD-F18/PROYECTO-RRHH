<?php

namespace Tests\Caracteristicas\Api\Pension;

use App\Models\Usuario;
use Tests\Soporte\Concerns\ConCabeceraAutenticacionJwt;
use Tests\Soporte\Concerns\ConPruebasModuloApi;
use Tests\TestCase;

class PensionApiTest extends TestCase
{
    use ConCabeceraAutenticacionJwt;
    use ConPruebasModuloApi;

    public function test_pensiones_crud_api(): void
    {
        $usuario = Usuario::factory()->create();
        $sufijo = uniqid();

        $this->probarCrudModuloApi(
            $usuario,
            '/api/v1/pensiones',
            [
                'nombre_fondo_pension' => "Pension {$sufijo}",
                'descripcion_fondo_pension' => 'Fondo pension prueba',
            ],
            [
                'nombre_fondo_pension' => "Pension Edit {$sufijo}",
                'descripcion_fondo_pension' => 'Fondo actualizado',
            ],
            'cod_fondo_pensiones'
        );
    }
}
