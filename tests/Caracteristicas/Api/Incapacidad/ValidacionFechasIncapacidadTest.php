<?php

namespace Tests\Caracteristicas\Api\Incapacidad;

use App\Models\Banco;
use App\Models\Cargo;
use App\Models\Contrato;
use App\Models\Empleado;
use App\Models\Incapacidad;
use App\Models\TipoIncapacidad;
use App\Models\Usuario;
use Tests\Soporte\Concerns\ConCabeceraAutenticacionJwt;
use Tests\TestCase;

class ValidacionFechasIncapacidadTest extends TestCase
{
    use ConCabeceraAutenticacionJwt;

    /** @return array{usuario: Usuario, empleado: Empleado, tipo: TipoIncapacidad} */
    private function crearEmpleadoConContrato(string $fechaNac, string $fechaIngreso, string $estadoContrato = 'ACTIVO'): array
    {
        $usuario = Usuario::factory()->create();
        $banco = Banco::create([
            'nombre_banco' => 'Banco Inc '.uniqid(),
            'descripcion_banco' => 'Prueba',
        ]);
        $cargo = Cargo::create([
            'nomb_cargo' => 'Cargo '.uniqid(),
            'descripcion' => 'Prueba',
        ]);

        $empleado = Empleado::create([
            'nombre_empleado' => 'Nombre',
            'apellidos_empleado' => 'Apellido',
            'doc_iden' => (string) random_int(10000000, 99999999),
            'tipo_documento' => 'CC',
            'fecha_nac' => $fechaNac,
            'direccion' => 'Calle 123 Bogotá D.C.',
            'numero_telefono' => '3001234567',
            'correo_empleado' => 'inc_'.uniqid('', true).'@prueba.local',
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

        Contrato::create([
            'tipo_contrato' => 'Término indefinido',
            'cod_empleado' => $empleado->cod_empleado,
            'forma_de_pago' => 'Mensual',
            'fecha_ingreso' => $fechaIngreso,
            'fecha_fin' => null,
            'salario_base' => 2000000,
            'cod_cargo' => $cargo->cod_cargo,
            'modalidad_trabajo' => 'Presencial',
            'horario_trabajo' => 'L-V',
            'auxilio_transporte' => false,
            'descripcion' => 'Contrato prueba',
            'estado_contrato' => $estadoContrato,
        ]);

        $tipo = TipoIncapacidad::query()->where('clave_normativa', 'origen_comun')->first();
        $this->assertNotNull($tipo);

        return ['usuario' => $usuario, 'empleado' => $empleado, 'tipo' => $tipo];
    }

    public function test_rechaza_fecha_inicio_anterior_a_fecha_ingreso_contrato(): void
    {
        $x = $this->crearEmpleadoConContrato('1990-01-01', '2020-06-01');

        $this->conJwt($x['usuario'])
            ->postJson('/api/v1/incapacidades', [
                'descripcion' => 'Test',
                'fecha_inicio' => '2020-05-01',
                'fecha_fin' => '2020-05-10',
                'fecha_radicacion' => null,
                'cod_tipo_incapacidad' => $x['tipo']->cod_tipo_incapacidad,
                'cod_empleado' => $x['empleado']->cod_empleado,
                'cod_clasificacion_enfermedad' => null,
                'estado_incapacidad' => 'Activa',
            ])
            ->assertStatus(422)
            ->assertJsonValidationErrors(['fecha_inicio']);
    }

    public function test_rechaza_fecha_antes_de_cumplir_15_anios(): void
    {
        $x = $this->crearEmpleadoConContrato('2010-06-01', '2026-01-01');

        $this->conJwt($x['usuario'])
            ->postJson('/api/v1/incapacidades', [
                'descripcion' => 'Test',
                'fecha_inicio' => '2025-05-15',
                'fecha_fin' => '2025-05-20',
                'cod_tipo_incapacidad' => $x['tipo']->cod_tipo_incapacidad,
                'cod_empleado' => $x['empleado']->cod_empleado,
                'estado_incapacidad' => 'Activa',
            ])
            ->assertStatus(422)
            ->assertJsonValidationErrors(['fecha_inicio']);
    }

    public function test_rechaza_fecha_radicacion_anterior_a_inicio(): void
    {
        $x = $this->crearEmpleadoConContrato('1990-01-01', '2020-01-01');

        $this->conJwt($x['usuario'])
            ->postJson('/api/v1/incapacidades', [
                'descripcion' => 'Test',
                'fecha_inicio' => '2024-06-10',
                'fecha_fin' => '2024-06-15',
                'fecha_radicacion' => '2024-06-05',
                'cod_tipo_incapacidad' => $x['tipo']->cod_tipo_incapacidad,
                'cod_empleado' => $x['empleado']->cod_empleado,
                'estado_incapacidad' => 'Activa',
            ])
            ->assertStatus(422)
            ->assertJsonValidationErrors(['fecha_radicacion']);
    }

    public function test_sin_contrato_rechaza_incapacidad(): void
    {
        $usuario = Usuario::factory()->create();
        $banco = Banco::create([
            'nombre_banco' => 'Banco SinC '.uniqid(),
            'descripcion_banco' => 'Prueba',
        ]);
        $empleado = Empleado::create([
            'nombre_empleado' => 'Nombre',
            'apellidos_empleado' => 'Apellido',
            'doc_iden' => (string) random_int(10000000, 99999999),
            'tipo_documento' => 'CC',
            'fecha_nac' => '1990-01-01',
            'direccion' => 'Calle 123 Bogotá D.C.',
            'numero_telefono' => '3001234567',
            'correo_empleado' => 'sinc_'.uniqid('', true).'@prueba.local',
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
        $tipo = TipoIncapacidad::query()->where('clave_normativa', 'origen_comun')->first();
        $this->assertNotNull($tipo);

        $this->conJwt($usuario)
            ->postJson('/api/v1/incapacidades', [
                'fecha_inicio' => '2024-01-10',
                'fecha_fin' => '2024-01-12',
                'cod_tipo_incapacidad' => $tipo->cod_tipo_incapacidad,
                'cod_empleado' => $empleado->cod_empleado,
                'estado_incapacidad' => 'Activa',
            ])
            ->assertStatus(422)
            ->assertJsonValidationErrors(['fecha_inicio']);
    }

    public function test_usa_fecha_mas_temprana_entre_contratos_activos(): void
    {
        $x = $this->crearEmpleadoConContrato('1990-01-01', '2022-01-01', 'ACTIVO');
        $cargo = Cargo::create(['nomb_cargo' => 'C2 '.uniqid(), 'descripcion' => 'x']);
        Contrato::create([
            'tipo_contrato' => 'Término indefinido',
            'cod_empleado' => $x['empleado']->cod_empleado,
            'forma_de_pago' => 'Mensual',
            'fecha_ingreso' => '2020-01-01',
            'fecha_fin' => null,
            'salario_base' => 2500000,
            'cod_cargo' => $cargo->cod_cargo,
            'modalidad_trabajo' => 'Presencial',
            'horario_trabajo' => 'L-V',
            'auxilio_transporte' => false,
            'descripcion' => 'Segundo contrato',
            'estado_contrato' => 'ACTIVO',
        ]);

        $this->conJwt($x['usuario'])
            ->postJson('/api/v1/incapacidades', [
                'fecha_inicio' => '2019-06-01',
                'fecha_fin' => '2019-06-05',
                'cod_tipo_incapacidad' => $x['tipo']->cod_tipo_incapacidad,
                'cod_empleado' => $x['empleado']->cod_empleado,
                'estado_incapacidad' => 'Activa',
            ])
            ->assertStatus(422)
            ->assertJsonValidationErrors(['fecha_inicio']);
    }

    public function test_sin_activo_usa_contrato_finalizado(): void
    {
        $x = $this->crearEmpleadoConContrato('1990-01-01', '2019-03-01', 'FINALIZADO');

        $this->conJwt($x['usuario'])
            ->postJson('/api/v1/incapacidades', [
                'fecha_inicio' => '2018-01-01',
                'fecha_fin' => '2018-01-05',
                'cod_tipo_incapacidad' => $x['tipo']->cod_tipo_incapacidad,
                'cod_empleado' => $x['empleado']->cod_empleado,
                'estado_incapacidad' => 'Activa',
            ])
            ->assertStatus(422)
            ->assertJsonValidationErrors(['fecha_inicio']);

        $this->conJwt($x['usuario'])
            ->postJson('/api/v1/incapacidades', [
                'fecha_inicio' => '2019-03-15',
                'fecha_fin' => '2019-03-20',
                'cod_tipo_incapacidad' => $x['tipo']->cod_tipo_incapacidad,
                'cod_empleado' => $x['empleado']->cod_empleado,
                'estado_incapacidad' => 'Activa',
            ])
            ->assertCreated();
    }

    public function test_patch_mantiene_validacion_con_empleado_de_la_incapacidad(): void
    {
        $x = $this->crearEmpleadoConContrato('1990-01-01', '2020-01-01');
        $inc = Incapacidad::create([
            'descripcion' => 'x',
            'fecha_inicio' => '2024-01-10',
            'fecha_fin' => '2024-01-12',
            'fecha_radicacion' => '2024-01-10',
            'cod_tipo_incapacidad' => $x['tipo']->cod_tipo_incapacidad,
            'cod_empleado' => $x['empleado']->cod_empleado,
            'cod_clasificacion_enfermedad' => null,
            'estado_incapacidad' => 'Activa',
        ]);

        $this->conJwt($x['usuario'])
            ->patchJson('/api/v1/incapacidades/'.$inc->cod_incapacidad, [
                'fecha_inicio' => '2019-01-01',
            ])
            ->assertStatus(422)
            ->assertJsonValidationErrors(['fecha_inicio']);
    }
}
