<?php

namespace Tests\Caracteristicas\Api\Inasistencia;

use App\Models\Usuario;
use Tests\Soporte\Concerns\ConCabeceraAutenticacionJwt;
use Tests\Soporte\Concerns\ConDatosPruebaRrhh;
use Tests\Soporte\Concerns\ConPruebasModuloApi;
use Tests\TestCase;

class InasistenciaApiTest extends TestCase
{
    use ConCabeceraAutenticacionJwt;
    use ConDatosPruebaRrhh;
    use ConPruebasModuloApi;

    public function test_inasistencias_crud_api(): void
    {
        $base = $this->crearEmpleadoConContrato();

        $this->probarCrudModuloApi(
            $base['usuario'],
            '/api/v1/inasistencias',
            [
                'motivo_inasistencia' => 'Cita medica',
                'fecha_inasistencia' => '2024-06-10',
                'cod_empleado' => $base['empleado']->cod_empleado,
                'observaciones' => 'Justificada',
                'justificado' => 'SI',
            ],
            [
                'motivo_inasistencia' => 'Calamidad',
                'fecha_inasistencia' => '2024-06-11',
                'observaciones' => 'Actualizada',
            ],
            'cod_inasistencias'
        );
    }
}
