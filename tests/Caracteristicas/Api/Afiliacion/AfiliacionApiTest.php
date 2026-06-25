<?php

namespace Tests\Caracteristicas\Api\Afiliacion;

use App\Models\Usuario;
use Tests\Soporte\Concerns\ConCabeceraAutenticacionJwt;
use Tests\Soporte\Concerns\ConDatosPruebaRrhh;
use Tests\Soporte\Concerns\ConPruebasModuloApi;
use Tests\TestCase;

class AfiliacionApiTest extends TestCase
{
    use ConCabeceraAutenticacionJwt;
    use ConDatosPruebaRrhh;
    use ConPruebasModuloApi;

    public function test_afiliaciones_crud_api(): void
    {
        $base = $this->crearEmpleadoConContrato();
        $catalogos = $this->crearCatalogosAfiliacion();

        $payload = $this->payloadAfiliacionApi(
            (int) $base['empleado']->cod_empleado,
            $catalogos
        );
        $payloadUpdate = array_merge($payload, [
            'descripcion' => 'Afiliacion actualizada por test',
            'estado_afiliacion' => 'Inactiva',
        ]);

        $this->probarCrudModuloApi(
            $base['usuario'],
            '/api/v1/afiliaciones',
            $payload,
            $payloadUpdate,
            'cod_afiliacion'
        );
    }
}
