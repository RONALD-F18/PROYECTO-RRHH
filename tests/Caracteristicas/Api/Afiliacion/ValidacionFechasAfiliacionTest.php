<?php

namespace Tests\Caracteristicas\Api\Afiliacion;

use App\Models\Afiliacion;
use App\Models\Arl;
use App\Models\Banco;
use App\Models\Cesantia;
use App\Models\Compensacion;
use App\Models\Empleado;
use App\Models\Eps;
use App\Models\Pension;
use App\Models\Riesgo;
use App\Models\Usuario;
use Tests\Soporte\Concerns\ConCabeceraAutenticacionJwt;
use Tests\TestCase;

/**
 * Afiliaciones: fechas de afiliación vs empleado persistido (nacimiento y edad mínima laboral).
 */
class ValidacionFechasAfiliacionTest extends TestCase
{
    use ConCabeceraAutenticacionJwt;

    /** @return array{usuario: Usuario, empleado: Empleado, ids: array<string, int>} */
    private function crearUniversoAfiliacion(string $fechaNac): array
    {
        $usuario = Usuario::factory()->create();
        $banco = Banco::create([
            'nombre_banco' => 'Banco T '.uniqid(),
            'descripcion_banco' => 'Prueba',
        ]);

        $empleado = Empleado::create([
            'nombre_empleado' => 'Nombre',
            'apellidos_empleado' => 'Apellido',
            'doc_iden' => (string) random_int(10000000, 99999999),
            'tipo_documento' => 'CC',
            'fecha_nac' => $fechaNac,
            'direccion' => 'Calle 123 Bogotá D.C.',
            'numero_telefono' => '3001234567',
            'correo_empleado' => 'emp_'.uniqid('', true).'@prueba.local',
            'numero_cuenta' => (string) random_int(1000000000, 9999999999),
            'tipo_cuenta' => 'AHORROS',
            'cod_banco' => $banco->cod_banco,
            'estado_emp' => 'ACTIVO',
            'discapacidad' => 'NINGUNA',
            'nacionalidad' => 'COLOMBIANA',
            'estado_civil' => 'SOLTERO',
            'grupo_sanguineo' => 'O+',
            'profesion' => 'Técnico',
            'fec_exp_doc' => '2018-06-01',
            'descripcion' => 'Empleado de prueba',
            'cod_usuario' => $usuario->cod_usuario,
        ]);

        $eps = Eps::create([
            'nombre_eps' => 'EPS T '.uniqid(),
            'descripcion_eps' => 'Prueba',
        ]);
        $arl = Arl::create([
            'nombre_arl' => 'ARL T '.uniqid(),
            'descripcion_arl' => 'Prueba',
        ]);
        $riesgo = Riesgo::create([
            'nombre_riesgo' => 'R T '.uniqid(),
            'descripcion_riesgo' => 'Prueba',
        ]);
        $pension = Pension::create([
            'nombre_fondo_pension' => 'FP T '.uniqid(),
            'descripcion_fondo_pension' => 'Prueba',
        ]);
        $cesantia = Cesantia::create([
            'nombre_fondo_cesantia' => 'FC T '.uniqid(),
            'descripcion_fondo_cesantia' => 'Prueba',
        ]);
        $caja = Compensacion::create([
            'nombre_caja_compensacion' => 'CC T '.uniqid(),
            'descripcion_caja_compensacion' => 'Prueba',
        ]);

        return [
            'usuario' => $usuario,
            'empleado' => $empleado,
            'ids' => [
                'cod_eps' => (int) $eps->cod_eps,
                'cod_arl' => (int) $arl->cod_arl,
                'cod_riesgo' => (int) $riesgo->cod_riesgo,
                'cod_fondo_pensiones' => (int) $pension->cod_fondo_pensiones,
                'cod_fondo_cesantias' => (int) $cesantia->cod_fondo_cesantias,
                'cod_caja_compensacion' => (int) $caja->cod_caja_compensacion,
            ],
        ];
    }

    /** @param array<string, string> $fechasAfiliacion */
    private function cuerpoAfiliacion(int $codEmpleado, array $ids, array $fechasAfiliacion): array
    {
        return array_merge([
            'estado_afiliacion' => 'ACTIVA',
            'cod_eps' => $ids['cod_eps'],
            'cod_arl' => $ids['cod_arl'],
            'cod_riesgo' => $ids['cod_riesgo'],
            'cod_fondo_pensiones' => $ids['cod_fondo_pensiones'],
            'cod_fondo_cesantias' => $ids['cod_fondo_cesantias'],
            'cod_caja_compensacion' => $ids['cod_caja_compensacion'],
            'cod_empleado' => $codEmpleado,
            'descripcion' => 'Afiliación de prueba',
            'tipo_regimen' => 'CONTRIBUTIVO',
        ], $fechasAfiliacion);
    }

    private function fechasValidas2024(): array
    {
        return [
            'fecha_afiliacion_eps' => '2024-06-01',
            'fecha_afiliacion_arl' => '2024-06-01',
            'fecha_afiliacion_caja' => '2024-06-01',
            'fecha_afiliacion_fondo_pensiones' => '2024-06-01',
            'fecha_afiliacion_fondo_cesantias' => '2024-06-01',
        ];
    }

    public function test_post_rechaza_fecha_afiliacion_anterior_al_nacimiento(): void
    {
        $u = $this->crearUniversoAfiliacion('2010-06-01');
        $fechas = $this->fechasValidas2024();
        $fechas['fecha_afiliacion_eps'] = '2009-01-01';

        $this->conJwt($u['usuario'])
            ->postJson('/api/v1/afiliaciones', $this->cuerpoAfiliacion(
                (int) $u['empleado']->cod_empleado,
                $u['ids'],
                $fechas
            ))
            ->assertStatus(422)
            ->assertJsonValidationErrors(['fecha_afiliacion_eps']);
    }

    public function test_post_rechaza_fecha_afiliacion_antes_de_cumplir_15_anios(): void
    {
        $u = $this->crearUniversoAfiliacion('2010-06-01');
        $fechas = $this->fechasValidas2024();
        $fechas['fecha_afiliacion_arl'] = '2014-06-01';

        $this->conJwt($u['usuario'])
            ->postJson('/api/v1/afiliaciones', $this->cuerpoAfiliacion(
                (int) $u['empleado']->cod_empleado,
                $u['ids'],
                $fechas
            ))
            ->assertStatus(422)
            ->assertJsonValidationErrors(['fecha_afiliacion_arl']);
    }

    public function test_post_acepta_fechas_cuando_cumple_restricciones_de_edad(): void
    {
        $u = $this->crearUniversoAfiliacion('2005-01-01');
        $fechas = $this->fechasValidas2024();

        $this->conJwt($u['usuario'])
            ->postJson('/api/v1/afiliaciones', $this->cuerpoAfiliacion(
                (int) $u['empleado']->cod_empleado,
                $u['ids'],
                $fechas
            ))
            ->assertCreated();
    }

    public function test_patch_valida_fecha_respecto_al_empleado_persistido_de_la_afiliacion(): void
    {
        $u = $this->crearUniversoAfiliacion('2008-03-10');
        $fechas = [
            'fecha_afiliacion_eps' => '2024-01-15',
            'fecha_afiliacion_arl' => '2024-01-16',
            'fecha_afiliacion_caja' => '2024-01-17',
            'fecha_afiliacion_fondo_pensiones' => '2024-01-18',
            'fecha_afiliacion_fondo_cesantias' => '2024-01-19',
        ];
        $afiliacion = Afiliacion::create($this->cuerpoAfiliacion(
            (int) $u['empleado']->cod_empleado,
            $u['ids'],
            $fechas
        ));

        $this->conJwt($u['usuario'])
            ->patchJson('/api/v1/afiliaciones/'.$afiliacion->cod_afiliacion, [
                'fecha_afiliacion_eps' => '2022-03-09',
            ])
            ->assertStatus(422)
            ->assertJsonValidationErrors(['fecha_afiliacion_eps']);
    }

    public function test_nacimiento_29_febrero_y_carbon_addYears_quince(): void
    {
        $u = $this->crearUniversoAfiliacion('2008-02-29');
        $fechas = $this->fechasValidas2024();
        $fechas['fecha_afiliacion_caja'] = '2023-02-27';

        $this->conJwt($u['usuario'])
            ->postJson('/api/v1/afiliaciones', $this->cuerpoAfiliacion(
                (int) $u['empleado']->cod_empleado,
                $u['ids'],
                $fechas
            ))
            ->assertStatus(422)
            ->assertJsonValidationErrors(['fecha_afiliacion_caja']);

        $fechas['fecha_afiliacion_caja'] = '2023-02-28';

        $this->conJwt($u['usuario'])
            ->postJson('/api/v1/afiliaciones', $this->cuerpoAfiliacion(
                (int) $u['empleado']->cod_empleado,
                $u['ids'],
                $fechas
            ))
            ->assertCreated();
    }
}
