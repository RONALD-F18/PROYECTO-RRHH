<?php

namespace Tests\Caracteristicas\Api\PrestacionSocial;

use App\Models\PrestacionSocialPeriodo;
use App\Models\Usuario;
use Tests\Soporte\Concerns\ConCabeceraAutenticacionJwt;
use Tests\Soporte\Concerns\ConDatosPruebaRrhh;
use Tests\TestCase;

class PrestacionSocialApiTest extends TestCase
{
    use ConCabeceraAutenticacionJwt;
    use ConDatosPruebaRrhh;

    public function test_prestaciones_sociales_endpoints(): void
    {
        $base = $this->crearEmpleadoConContrato();
        $usuario = $base['usuario'];
        $contrato = $base['contrato'];

        $this->getJson('/api/v1/prestaciones-sociales')->assertUnauthorized();

        $this->conJwt($usuario)
            ->getJson('/api/v1/prestaciones-sociales')
            ->assertOk()
            ->assertJsonStructure([
                'message',
                'data' => ['totales_pendientes', 'contratos_vigentes'],
            ]);

        $this->conJwt($usuario)
            ->getJson('/api/v1/prestaciones-sociales/totales')
            ->assertOk()
            ->assertJsonStructure(['message', 'data']);

        $this->conJwt($usuario)
            ->getJson('/api/v1/prestaciones-sociales/listar')
            ->assertOk()
            ->assertJsonStructure(['message', 'data']);

        $this->conJwt($usuario)
            ->getJson('/api/v1/contratos/'.$contrato->cod_contrato.'/prestaciones')
            ->assertOk()
            ->assertJsonStructure(['message', 'data']);

        $calcular = $this->conJwt($usuario)
            ->postJson('/api/v1/contratos/'.$contrato->cod_contrato.'/calcular-prestaciones')
            ->assertCreated()
            ->assertJsonStructure(['message', 'data']);

        $idPrestacion = data_get($calcular->json(), 'data.cod_prestacion_social_periodo');
        $this->assertNotNull($idPrestacion);

        $this->conJwt($usuario)
            ->postJson('/api/v1/prestaciones-sociales/gestionar', [
                'cod_prestacion_social_periodo' => $idPrestacion,
                'estado_pago' => 'Pagado',
            ])
            ->assertOk();

        $this->conJwt($usuario)
            ->deleteJson('/api/v1/prestaciones-sociales/'.$idPrestacion)
            ->assertStatus(422);
    }

    public function test_prestaciones_eliminar_periodo_pendiente(): void
    {
        $base = $this->crearEmpleadoConContrato();

        $periodo = PrestacionSocialPeriodo::create([
            'cod_contrato' => $base['contrato']->cod_contrato,
            'fecha_periodo_inicio' => '2024-01-01',
            'fecha_periodo_fin' => '2024-06-30',
            'dias_trabajados' => 180,
            'salario_base' => 2500000,
            'auxilio_transporte' => 140606,
            'cesantias_valor' => 100000,
            'intereses_cesantias_valor' => 12000,
            'prima_valor' => 50000,
            'vacaciones_valor' => 30000,
            'estado_pago' => 'Pendiente',
            'fecha_calculo' => '2024-06-30',
        ]);

        $this->conJwt($base['usuario'])
            ->deleteJson('/api/v1/prestaciones-sociales/'.$periodo->cod_prestacion_social_periodo)
            ->assertOk();
    }
}
