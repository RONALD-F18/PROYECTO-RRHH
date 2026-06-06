<?php

namespace Tests\Caracteristicas\Api\Contrato;

use App\Models\Banco;
use App\Models\Cargo;
use App\Models\Contrato;
use App\Models\Empleado;
use App\Models\Usuario;
use Tests\Soporte\Concerns\ConCabeceraAutenticacionJwt;
use Tests\TestCase;

class ContratoPaginacionTest extends TestCase
{
    use ConCabeceraAutenticacionJwt;

    /** @return array{usuario: Usuario, empleado: Empleado, cargo: Cargo} */
    private function baseEmpleado(Usuario $usuario): array
    {
        $banco = Banco::create([
            'nombre_banco' => 'Banco Con '.uniqid(),
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
            'fecha_nac' => '1990-01-01',
            'direccion' => 'Calle 123',
            'numero_telefono' => '3001234567',
            'correo_empleado' => 'con_'.uniqid('', true).'@prueba.local',
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
            'descripcion' => 'Empleado contrato pag',
            'cod_usuario' => $usuario->cod_usuario,
        ]);

        return ['usuario' => $usuario, 'empleado' => $empleado, 'cargo' => $cargo];
    }

    private function crearContrato(Empleado $empleado, Cargo $cargo): Contrato
    {
        return Contrato::create([
            'tipo_contrato' => 'Término indefinido',
            'cod_empleado' => $empleado->cod_empleado,
            'forma_de_pago' => 'Mensual',
            'fecha_ingreso' => '2020-01-01',
            'fecha_fin' => null,
            'salario_base' => 2000000,
            'cod_cargo' => $cargo->cod_cargo,
            'modalidad_trabajo' => 'Presencial',
            'horario_trabajo' => 'L-V',
            'auxilio_transporte' => false,
            'descripcion' => 'Contrato pag',
            'estado_contrato' => 'ACTIVO',
        ]);
    }

    public function test_contratos_index_devuelve_meta(): void
    {
        $usuario = Usuario::factory()->create();
        $base = $this->baseEmpleado($usuario);
        $this->crearContrato($base['empleado'], $base['cargo']);
        $this->crearContrato($base['empleado'], $base['cargo']);

        $this->conJwt($usuario)
            ->getJson('/api/v1/contratos?page=1&per_page=1')
            ->assertOk()
            ->assertJsonStructure([
                'message',
                'data',
                'meta' => ['current_page', 'per_page', 'total', 'last_page'],
            ])
            ->assertJsonPath('meta.per_page', 1)
            ->assertJsonPath('meta.total', 2)
            ->assertJsonCount(1, 'data');
    }
}
