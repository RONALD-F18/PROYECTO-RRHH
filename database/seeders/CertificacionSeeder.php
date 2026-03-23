<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class CertificacionSeeder extends Seeder
{
    public function run(): void
    {
        $idEmpresa   = 1;
        $codEmpleado = 1;
        $codContrato = 1;

        DB::table('certificaciones')->insert([
            [
                'id_empresa'          => $idEmpresa,
                'cod_empleado'        => $codEmpleado,
                'cod_contrato'        => $codContrato,
                'tipo_certificacion'  => 'LABORAL',
                'incluye_salario'     => true,
                'salario_certificado' => 2800000,
                'cod_eps'             => null,
                'cod_arl'             => null,
                'cod_pension'         => null,
                'cod_caja'            => null,
                'cod_cesantias'       => null,
                'fecha_emision'       => now()->toDateString(),
                'ciudad_emision'      => 'Medellín',
                'descripcion'         => 'Certificación generada automáticamente para prueba.',
                'created_at'          => now(),
                'updated_at'          => now(),
            ],
        ]);
    }
}

