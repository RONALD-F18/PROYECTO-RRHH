<?php

namespace Tests\Caracteristicas\Api\Dashboard;

use App\Models\Usuario;
use Tests\Soporte\Concerns\ConCabeceraAutenticacionJwt;
use Tests\TestCase;

class DashboardResumenTest extends TestCase
{
    use ConCabeceraAutenticacionJwt;

    public function test_dashboard_resumen_requiere_autenticacion(): void
    {
        $this->getJson('/api/v1/dashboard/resumen')->assertUnauthorized();
    }

    public function test_dashboard_resumen_devuelve_agregados(): void
    {
        $usuario = Usuario::factory()->create();

        $this->conJwt($usuario)
            ->getJson('/api/v1/dashboard/resumen')
            ->assertOk()
            ->assertJsonPath('message', 'Resumen del dashboard obtenido exitosamente')
            ->assertJsonStructure([
                'data' => [
                    'empleados_activos',
                    'contratos_vigentes',
                    'contratos_otros',
                    'inasistencias_mes_actual',
                    'incapacidades_total',
                    'afiliaciones_total',
                    'certificaciones_total',
                    'inasistencias_ultimos_6_meses',
                    'contratos_pie',
                    'actividades_recientes',
                ],
            ])
            ->assertJsonCount(6, 'data.inasistencias_ultimos_6_meses')
            ->assertJsonCount(2, 'data.contratos_pie');
    }
}
