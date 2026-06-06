<?php

namespace Tests\Caracteristicas\Api\Empresa;

use App\Models\Usuario;
use Tests\Soporte\Concerns\ConCabeceraAutenticacionJwt;
use Tests\Soporte\Concerns\ConPruebasModuloApi;
use Tests\TestCase;

class EmpresaApiTest extends TestCase
{
    use ConCabeceraAutenticacionJwt;
    use ConPruebasModuloApi;

    public function test_empresas_crud_api(): void
    {
        $usuario = Usuario::factory()->create();
        $nit = (string) random_int(900000000, 999999999);

        $this->probarCrudModuloApi(
            $usuario,
            '/api/v1/empresas',
            [
                'nit' => $nit,
                'dv' => '1',
                'razon_social' => 'Empresa Test SAS',
                'nombre_comercial' => 'Empresa Test',
                'tipo_empresa' => 'Privada',
                'estado_empresa' => 'Activa',
                'ciudad' => 'Bogota',
                'pais' => 'Colombia',
            ],
            [
                'razon_social' => 'Empresa Test Actualizada SAS',
                'nombre_comercial' => 'Empresa Edit',
            ],
            'id_empresa',
            200
        );
    }
}
