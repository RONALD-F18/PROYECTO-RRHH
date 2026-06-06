<?php

namespace Tests\Caracteristicas\Api\Calendario;

use App\Models\Usuario;
use Tests\Soporte\Concerns\ConCabeceraAutenticacionJwt;
use Tests\Soporte\Concerns\ConPruebasModuloApi;
use Tests\TestCase;

class ActividadCalendarioApiTest extends TestCase
{
    use ConCabeceraAutenticacionJwt;
    use ConPruebasModuloApi;

    public function test_calendario_actividades_crud_api(): void
    {
        $usuario = Usuario::factory()->create();

        $this->probarCrudModuloApi(
            $usuario,
            '/api/v1/calendario-actividades',
            [
                'titulo' => 'Reunion RRHH',
                'tipo' => 'Reunion',
                'fecha_inicio' => '2025-01-10',
                'fecha_fin' => '2025-01-10',
                'estado' => 'Pendiente',
                'prioridad' => 'Media',
                'cod_usuario' => $usuario->cod_usuario,
                'descripcion' => 'Revision mensual',
            ],
            [
                'titulo' => 'Reunion RRHH Edit',
                'estado' => 'Completada',
            ],
            'cod_actividad'
        );
    }
}
