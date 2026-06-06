<?php

namespace Tests\Caracteristicas\Api\Incapacidad;

use App\Models\TipoIncapacidad;
use App\Models\Usuario;
use Tests\Soporte\Concerns\ConCabeceraAutenticacionJwt;
use Tests\Soporte\Concerns\ConDatosPruebaRrhh;
use Tests\Soporte\Concerns\ConPruebasModuloApi;
use Tests\TestCase;

class IncapacidadApiTest extends TestCase
{
    use ConCabeceraAutenticacionJwt;
    use ConDatosPruebaRrhh;
    use ConPruebasModuloApi;

    protected function setUp(): void
    {
        parent::setUp();
        $this->sembrarTiposIncapacidad();
    }

    public function test_incapacidades_crud_y_resumen_api(): void
    {
        $base = $this->crearEmpleadoConContrato();
        $tipo = TipoIncapacidad::query()->where('clave_normativa', 'origen_comun')->firstOrFail();

        $this->probarCrudModuloApi(
            $base['usuario'],
            '/api/v1/incapacidades',
            [
                'fecha_inicio' => '2024-03-01',
                'fecha_fin' => '2024-03-05',
                'fecha_radicacion' => '2024-03-01',
                'cod_tipo_incapacidad' => $tipo->cod_tipo_incapacidad,
                'cod_empleado' => $base['empleado']->cod_empleado,
                'estado_incapacidad' => 'Activa',
                'descripcion' => 'Incapacidad prueba',
            ],
            [
                'estado_incapacidad' => 'Finalizada',
                'descripcion' => 'Incapacidad finalizada',
            ],
            'cod_incapacidad'
        );

        $this->conJwt($base['usuario'])
            ->getJson('/api/v1/incapacidades/resumen')
            ->assertOk()
            ->assertJsonStructure(['message', 'data']);

        $base2 = $this->crearEmpleadoConContrato();
        $this->conJwt($base2['usuario'])
            ->getJson('/api/v1/empleados/'.$base2['empleado']->cod_empleado.'/incapacidades')
            ->assertOk();
    }
}
