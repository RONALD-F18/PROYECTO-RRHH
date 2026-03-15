<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class ContratoSeeder extends Seeder
{
    public function run(): void
    {
        $empleado1 = DB::table('empleados')->where('doc_iden', '7954321012')->first();
        $empleado2 = DB::table('empleados')->where('doc_iden', '5287654321')->first();

        if (!$empleado1 || !$empleado2) {
            return;
        }

        $contratos = [
            [
                'tipo_contrato'      => 'Contrato a término indefinido',
                'cod_empleado'       => $empleado1->cod_empleado,
                'forma_de_pago'      => 'Mensual',
                'fecha_ingreso'     => '2024-01-15',
                'fecha_fin'         => null,
                'salario_base'      => 2800000,
                'cod_cargo'         => 1,
                'modalidad_trabajo' => 'Presencial',
                'horario_trabajo'   => 'Lunes a viernes 8:00 a 17:00',
                'auxilio_transporte'=> true,
                'descripcion'       => 'Contrato laboral bajo normativa colombiana (Código Sustantivo del Trabajo).',
                'estado_contrato'   => 'ACTIVO',
                'created_at'        => now(),
                'updated_at'        => now(),
            ],
            [
                'tipo_contrato'      => 'Contrato a término indefinido',
                'cod_empleado'       => $empleado2->cod_empleado,
                'forma_de_pago'      => 'Mensual',
                'fecha_ingreso'     => '2024-03-01',
                'fecha_fin'         => null,
                'salario_base'      => 3500000,
                'cod_cargo'         => 2,
                'modalidad_trabajo' => 'Presencial',
                'horario_trabajo'   => 'Lunes a viernes 8:00 a 17:00',
                'auxilio_transporte'=> true,
                'descripcion'       => 'Contrato laboral bajo normativa colombiana (Código Sustantivo del Trabajo).',
                'estado_contrato'   => 'ACTIVO',
                'created_at'        => now(),
                'updated_at'        => now(),
            ],
        ];

        DB::table('contrato')->insert($contratos);
    }
}
