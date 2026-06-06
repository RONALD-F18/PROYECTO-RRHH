<?php

namespace Tests\Caracteristicas\Api\Banco;

use App\Models\Usuario;
use Tests\Soporte\Concerns\ConCabeceraAutenticacionJwt;
use Tests\Soporte\Concerns\ConPruebasModuloApi;
use Tests\TestCase;

class BancoApiTest extends TestCase
{
    use ConCabeceraAutenticacionJwt;
    use ConPruebasModuloApi;

    public function test_bancos_crud_api(): void
    {
        $usuario = Usuario::factory()->create();
        $sufijo = uniqid();

        $this->probarCrudModuloApi(
            $usuario,
            '/api/v1/bancos',
            [
                'nombre_banco' => "Banco API {$sufijo}",
                'descripcion_banco' => 'Descripcion inicial',
            ],
            [
                'nombre_banco' => "Banco API Edit {$sufijo}",
                'descripcion_banco' => 'Descripcion actualizada',
            ],
            'cod_banco'
        );
    }
}
