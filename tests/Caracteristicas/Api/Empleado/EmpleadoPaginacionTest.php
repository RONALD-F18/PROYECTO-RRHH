<?php

namespace Tests\Caracteristicas\Api\Empleado;

use App\Models\Banco;
use App\Models\Cargo;
use App\Models\Contrato;
use App\Models\Empleado;
use App\Models\Usuario;
use Tests\Soporte\Concerns\ConCabeceraAutenticacionJwt;
use Tests\TestCase;

class EmpleadoPaginacionTest extends TestCase
{
    use ConCabeceraAutenticacionJwt;

    private function crearEmpleado(Usuario $usuario): Empleado
    {
        $banco = Banco::create([
            'nombre_banco' => 'Banco Pag '.uniqid(),
            'descripcion_banco' => 'Prueba',
        ]);

        return Empleado::create([
            'nombre_empleado' => 'Nombre',
            'apellidos_empleado' => 'Apellido',
            'doc_iden' => (string) random_int(10000000, 99999999),
            'tipo_documento' => 'CC',
            'fecha_nac' => '1990-01-01',
            'direccion' => 'Calle 123',
            'numero_telefono' => '3001234567',
            'correo_empleado' => 'pag_'.uniqid('', true).'@prueba.local',
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
            'descripcion' => 'Empleado paginación',
            'cod_usuario' => $usuario->cod_usuario,
        ]);
    }

    public function test_empleados_index_requiere_autenticacion(): void
    {
        $this->getJson('/api/v1/empleados')->assertUnauthorized();
    }

    public function test_empleados_index_devuelve_meta_y_pagina(): void
    {
        $usuario = Usuario::factory()->create();
        $this->crearEmpleado($usuario);
        $this->crearEmpleado($usuario);
        $this->crearEmpleado($usuario);

        $this->conJwt($usuario)
            ->getJson('/api/v1/empleados?page=1&per_page=2')
            ->assertOk()
            ->assertJsonPath('meta.current_page', 1)
            ->assertJsonPath('meta.per_page', 2)
            ->assertJsonPath('meta.total', 3)
            ->assertJsonPath('meta.last_page', 2)
            ->assertJsonCount(2, 'data');
    }

    public function test_empleados_segunda_pagina(): void
    {
        $usuario = Usuario::factory()->create();
        $this->crearEmpleado($usuario);
        $this->crearEmpleado($usuario);
        $this->crearEmpleado($usuario);

        $this->conJwt($usuario)
            ->getJson('/api/v1/empleados?page=2&per_page=2')
            ->assertOk()
            ->assertJsonPath('meta.current_page', 2)
            ->assertJsonCount(1, 'data');
    }
}
