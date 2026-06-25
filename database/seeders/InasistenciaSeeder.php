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

        if (! $empleado1 || ! $empleado2) {
            return;
        }

        $registros = [
            [
                'motivo_inasistencia' => 'Cita medica',
                'fecha_inasistencia' => now()->subDays(18)->toDateString(),
                'cod_empleado' => $empleado1->cod_empleado,
                'observaciones' => 'Presento soporte medico de consulta general.',
                'justificado' => 'SI',
            ],
            [
                'motivo_inasistencia' => 'Tardanza por transporte',
                'fecha_inasistencia' => now()->subDays(14)->toDateString(),
                'cod_empleado' => $empleado1->cod_empleado,
                'observaciones' => 'No llego por cierre vial en su ruta habitual.',
                'justificado' => 'NO',
            ],
            [
                'motivo_inasistencia' => 'Dia libre compensatorio',
                'fecha_inasistencia' => now()->subDays(11)->toDateString(),
                'cod_empleado' => $empleado2->cod_empleado,
                'observaciones' => 'Compensacion por trabajo en jornada adicional.',
                'justificado' => 'SI',
            ],
            [
                'motivo_inasistencia' => 'Permiso personal',
                'fecha_inasistencia' => now()->subDays(9)->toDateString(),
                'cod_empleado' => $empleado2->cod_empleado,
                'observaciones' => 'Permiso autorizado para diligencia bancaria.',
                'justificado' => 'SI',
            ],
            [
                'motivo_inasistencia' => 'Ausencia sin aviso',
                'fecha_inasistencia' => now()->subDays(6)->toDateString(),
                'cod_empleado' => $empleado1->cod_empleado,
                'observaciones' => 'No presento solicitud previa formal.',
                'justificado' => 'NO',
            ],
            [
                'motivo_inasistencia' => 'Permiso academico',
                'fecha_inasistencia' => now()->subDays(3)->toDateString(),
                'cod_empleado' => $empleado2->cod_empleado,
                'observaciones' => 'Asistencia a evaluacion de formacion profesional.',
                'justificado' => 'SI',
            ],
        ];

        foreach ($registros as $data) {
            $data['created_at'] = now();
            $data['updated_at'] = now();

            DB::table('inasistencias')->updateOrInsert(
                [
                    'cod_empleado' => $data['cod_empleado'],
                    'fecha_inasistencia' => $data['fecha_inasistencia'],
                    'motivo_inasistencia' => $data['motivo_inasistencia'],
                ],
                $data
            );
        }
    }
}
