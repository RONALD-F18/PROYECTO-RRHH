<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class InasistenciaSeeder extends Seeder
{
    public function run(): void
    {
        $empleado1 = DB::table('empleados')->where('doc_iden', '7954321012')->first();
        $empleado2 = DB::table('empleados')->where('doc_iden', '5287654321')->first();

        if (!$empleado1 || !$empleado2) {
            return;
        }

        DB::table('inasistencias')->delete();

        DB::table('inasistencias')->insert([
            [
                'motivo_inasistencia' => 'Cita médica',
                'fecha_inasistencia'  => now()->subDays(18)->toDateString(),
                'cod_empleado'        => $empleado1->cod_empleado,
                'observaciones'       => 'Presentó soporte médico de consulta general.',
                'justificado'         => 'SI',
                'created_at'          => now(),
                'updated_at'          => now(),
            ],
            [
                'motivo_inasistencia' => 'Tardanza por transporte',
                'fecha_inasistencia'  => now()->subDays(14)->toDateString(),
                'cod_empleado'        => $empleado1->cod_empleado,
                'observaciones'       => 'No llego por cierre vial en su ruta habitual.',
                'justificado'         => 'NO',
                'created_at'          => now(),
                'updated_at'          => now(),
            ],
            [
                'motivo_inasistencia' => 'Día libre compensatorio',
                'fecha_inasistencia'  => now()->subDays(11)->toDateString(),
                'cod_empleado'        => $empleado2->cod_empleado,
                'observaciones'       => 'Compensación por trabajo en jornada adicional.',
                'justificado'         => 'SI',
                'created_at'          => now(),
                'updated_at'          => now(),
            ],
            [
                'motivo_inasistencia' => 'Permiso personal',
                'fecha_inasistencia'  => now()->subDays(9)->toDateString(),
                'cod_empleado'        => $empleado2->cod_empleado,
                'observaciones'       => 'Permiso autorizado para diligencia bancaria.',
                'justificado'         => 'SI',
                'created_at'          => now(),
                'updated_at'          => now(),
            ],
            [
                'motivo_inasistencia' => 'Ausencia sin aviso',
                'fecha_inasistencia'  => now()->subDays(6)->toDateString(),
                'cod_empleado'        => $empleado1->cod_empleado,
                'observaciones'       => 'No presento solicitud previa formal.',
                'justificado'         => 'NO',
                'created_at'          => now(),
                'updated_at'          => now(),
            ],
            [
                'motivo_inasistencia' => 'Permiso académico',
                'fecha_inasistencia'  => now()->subDays(3)->toDateString(),
                'cod_empleado'        => $empleado2->cod_empleado,
                'observaciones'       => 'Asistencia a evaluación de formación profesional.',
                'justificado'         => 'SI',
                'created_at'          => now(),
                'updated_at'          => now(),
            ],
        ]);
    }
}

