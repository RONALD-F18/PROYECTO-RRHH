<?php

namespace Tests\Caracteristicas\Api\Contrato;

use App\Models\Usuario;
use Tests\Soporte\Concerns\ConCabeceraAutenticacionJwt;
use Tests\Soporte\Concerns\ConDatosPruebaRrhh;
use Tests\Soporte\Concerns\ConPruebasModuloApi;
use Tests\TestCase;

class ContratoApiTest extends TestCase
{
    use ConCabeceraAutenticacionJwt;
    use ConDatosPruebaRrhh;
    use ConPruebasModuloApi;

    public function test_contratos_crud_api(): void
    {
        $base = $this->crearEmpleadoConContrato();
        $payload = $this->payloadContratoApi(
            (int) $base['empleado']->cod_empleado,
            (int) $base['cargo']->cod_cargo,
            ['fecha_ingreso' => '2021-06-01']
        );
        $payloadUpdate = array_merge($payload, [
            'salario_base' => 3000000,
            'descripcion' => 'Contrato actualizado por test',
        ]);

        $this->probarCrudModuloApiPlano(
            $base['usuario'],
            '/api/v1/contratos',
            $payload,
            $payloadUpdate,
            'cod_contrato'
        );
    }
}
