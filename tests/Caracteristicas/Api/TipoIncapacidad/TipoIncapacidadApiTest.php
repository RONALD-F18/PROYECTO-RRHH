<?php

namespace Tests\Caracteristicas\Api\TipoIncapacidad;

use App\Models\Usuario;
use Tests\Soporte\Concerns\ConCabeceraAutenticacionJwt;
use Tests\Soporte\Concerns\ConPruebasModuloApi;
use Tests\TestCase;

class TipoIncapacidadApiTest extends TestCase
{
    use ConCabeceraAutenticacionJwt;
    use ConPruebasModuloApi;

    public function test_tipos_incapacidad_crud_api(): void
    {
        $usuario = Usuario::factory()->create();
        $sufijo = uniqid();

        $this->probarCrudModuloApi(
            $usuario,
            '/api/v1/tipos-incapacidad',
            [
                'nombre_tipo' => "Tipo Inc {$sufijo}",
                'descripcion' => 'Tipo prueba',
                'clave_normativa' => 'origen_comun',
            ],
            [
                'nombre_tipo' => "Tipo Inc Edit {$sufijo}",
                'descripcion' => 'Tipo actualizado',
            ],
            'cod_tipo_incapacidad'
        );
    }
}
