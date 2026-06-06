<?php

namespace Tests\Caracteristicas\Api\Reportes;

use App\Models\Usuario;
use Tests\Soporte\Concerns\ConCabeceraAutenticacionJwt;
use Tests\TestCase;

class ReporteGenerarApiTest extends TestCase
{
    use ConCabeceraAutenticacionJwt;

    public function test_generar_reporte_pdf_empleados(): void
    {
        $usuario = Usuario::factory()->create();

        $this->postJson('/api/v1/reportes/generar', [
            'modulo' => 'empleados',
            'tipo' => 'resumen',
        ])->assertUnauthorized();

        $this->conJwt($usuario)
            ->postJson('/api/v1/reportes/generar', [
                'modulo' => 'empleados',
                'tipo' => 'resumen',
                'params' => ['descripcion' => 'Reporte test empleados'],
            ])
            ->assertOk()
            ->assertHeader('content-type', 'application/pdf');
    }

    public function test_generar_reporte_modulo_invalido(): void
    {
        $usuario = Usuario::factory()->create();

        $this->conJwt($usuario)
            ->postJson('/api/v1/reportes/generar', [
                'modulo' => 'modulo_inexistente',
                'tipo' => 'resumen',
            ])
            ->assertStatus(422)
            ->assertJsonValidationErrors(['modulo']);
    }
}
