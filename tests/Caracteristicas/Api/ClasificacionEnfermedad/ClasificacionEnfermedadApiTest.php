<?php

namespace Tests\Caracteristicas\Api\ClasificacionEnfermedad;

use App\Models\Usuario;
use Tests\Soporte\Concerns\ConCabeceraAutenticacionJwt;
use Tests\Soporte\Concerns\ConPruebasModuloApi;
use Tests\TestCase;

class ClasificacionEnfermedadApiTest extends TestCase
{
    use ConCabeceraAutenticacionJwt;
    use ConPruebasModuloApi;

    public function test_clasificaciones_enfermedad_crud_api(): void
    {
        $usuario = Usuario::factory()->create();
        $sufijo = uniqid();

        $this->probarCrudModuloApi(
            $usuario,
            '/api/v1/clasificaciones-enfermedad',
            [
                'nombre_clasificacion' => "Clasificacion {$sufijo}",
                'codigo_cie10' => 'J00',
                'descripcion' => 'Rinofaringitis aguda',
            ],
            [
                'nombre_clasificacion' => "Clasificacion Edit {$sufijo}",
                'descripcion' => 'Actualizada',
            ],
            'cod_clasificacion_enfermedad'
        );
    }
}
