<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class EmpleadoSeeder extends Seeder
{
    public function run(): void
    {
        $funcionario = DB::table('usuarios')
            ->join('roles', 'usuarios.cod_rol', '=', 'roles.cod_rol')
            ->where('roles.nombre_rol', 'funcionario')
            ->orderBy('usuarios.cod_usuario')
            ->select('usuarios.*')
            ->first();

        if (! $funcionario) {
            return;
        }

        $codUsuario = $funcionario->cod_usuario;
        $ahora = now();

        $empleados = [
            [
                'nombre_empleado'    => 'Carlos',
                'apellidos_empleado' => 'Perez Gomez',
                'doc_iden'           => '7954321012',
                'tipo_documento'     => 'CC',
                'fecha_nac'          => '1990-02-15',
                'sexo'               => 'Masculino',
                'direccion'          => 'Calle 23 Numero 45-67 Bogota',
                'numero_telefono'    => '3101234567',
                'correo_empleado'    => 'carlos.perez@gmail.com',
                'numero_cuenta'      => '111222333444',
                'tipo_cuenta'        => 'AHORROS',
                'cod_banco'          => 80001,
                'estado_emp'         => 'ACTIVO',
                'discapacidad'       => 'NINGUNA',
                'nacionalidad'       => 'Colombiana',
                'estado_civil'       => 'SOLTERO',
                'grupo_sanguineo'    => 'O+',
                'profesion'          => 'Ingeniero Industrial',
                'fec_exp_doc'        => '2012-05-10',
                'descripcion'        => 'Empleado del area de operaciones.',
                'cod_usuario'        => $codUsuario,
                'created_at'         => $ahora,
                'updated_at'         => $ahora,
            ],
            [
                'nombre_empleado'    => 'Ana',
                'apellidos_empleado' => 'Martinez Lopez',
                'doc_iden'           => '5287654321',
                'tipo_documento'     => 'CC',
                'fecha_nac'          => '1988-07-22',
                'sexo'               => 'Femenino',
                'direccion'          => 'Carrera 56 Numero 78-90 Bogota',
                'numero_telefono'    => '3209876543',
                'correo_empleado'    => 'ana.martinez@gmail.com',
                'numero_cuenta'      => '555666777888',
                'tipo_cuenta'        => 'CORRIENTE',
                'cod_banco'          => 80002,
                'estado_emp'         => 'ACTIVO',
                'discapacidad'       => 'NINGUNA',
                'nacionalidad'       => 'Colombiana',
                'estado_civil'       => 'CASADO',
                'grupo_sanguineo'    => 'A+',
                'profesion'          => 'Contadora Publica',
                'fec_exp_doc'        => '2011-09-08',
                'descripcion'        => 'Empleada del area financiera.',
                'cod_usuario'        => $codUsuario,
                'created_at'         => $ahora,
                'updated_at'         => $ahora,
            ],
        ];

        foreach ($empleados as $empleado) {
            DB::table('empleados')->updateOrInsert(
                ['doc_iden' => $empleado['doc_iden']],
                $empleado
            );
        }
    }
}
