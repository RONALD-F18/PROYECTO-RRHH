<?php

namespace Tests\Caracteristicas\Api\ComunicacionDisciplinaria;

use App\Models\Usuario;
use Tests\Soporte\Concerns\ConCabeceraAutenticacionJwt;
use Tests\Soporte\Concerns\ConDatosPruebaRrhh;
use Tests\Soporte\Concerns\ConPruebasModuloApi;
use Tests\TestCase;

class ComunicacionDisciplinariaApiTest extends TestCase
{
    use ConCabeceraAutenticacionJwt;
    use ConDatosPruebaRrhh;
    use ConPruebasModuloApi;

    public function test_comunicaciones_disciplinarias_crud_api(): void
    {
        $base = $this->crearEmpleadoConContrato();

        $this->probarCrudModuloApi(
            $base['usuario'],
            '/api/v1/comunicaciones_disciplinarias',
            [
                'tipo_comunicacion' => 'MEMORANDO',
                'fecha_emision' => '2024-05-01',
                'fecha_inicio_suspension' => '2024-05-02',
                'fecha_fin_suspension' => '2024-05-02',
                'dias_suspension' => 1,
                'estado_comunicacion' => 'EMITIDO',
                'motivo_comunicacion' => 'Retraso',
                'descripcion' => 'Retardo reiterado',
                'cod_empleado' => $base['empleado']->cod_empleado,
            ],
            [
                'estado_comunicacion' => 'NOTIFICADO',
                'motivo_comunicacion' => 'Retraso',
            ],
            'cod_disciplinario'
        );
    }
}
