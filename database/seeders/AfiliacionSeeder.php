<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class AfiliacionSeeder extends Seeder
{
    public function run(): void
    {
        $empleado1 = DB::table('empleados')->where('doc_iden', '7954321012')->first();
        $empleado2 = DB::table('empleados')->where('doc_iden', '5287654321')->first();

        if (! $empleado1 || ! $empleado2) {
            return;
        }

        $epsIds = DB::table('eps')->orderBy('cod_eps')->pluck('cod_eps')->toArray();
        $arlIds = DB::table('arls')->orderBy('cod_arl')->pluck('cod_arl')->toArray();
        $riesgoIds = DB::table('riesgos')->orderBy('cod_riesgo')->pluck('cod_riesgo')->toArray();
        $pensionIds = DB::table('fondo_pensiones')->orderBy('cod_fondo_pensiones')->pluck('cod_fondo_pensiones')->toArray();
        $cesantiaIds = DB::table('fondo_cesantias')->orderBy('cod_fondo_cesantias')->pluck('cod_fondo_cesantias')->toArray();
        $cajaIds = DB::table('caja_compensaciones')->orderBy('cod_caja_compensacion')->pluck('cod_caja_compensacion')->toArray();

        if (
            empty($epsIds) ||
            empty($arlIds) ||
            empty($riesgoIds) ||
            empty($pensionIds) ||
            empty($cesantiaIds) ||
            empty($cajaIds)
        ) {
            return;
        }

        $afiliaciones = [
            [
                'fecha_afiliacion_eps' => '2024-01-15',
                'fecha_afiliacion_arl' => '2024-01-15',
                'fecha_afiliacion_caja' => '2024-01-15',
                'fecha_afiliacion_fondo_pensiones' => '2024-01-15',
                'fecha_afiliacion_fondo_cesantias' => '2024-01-15',
                'estado_afiliacion' => 'Activa',
                'cod_eps' => $epsIds[0],
                'cod_arl' => $arlIds[0],
                'cod_riesgo' => $riesgoIds[0],
                'cod_fondo_pensiones' => $pensionIds[0],
                'cod_fondo_cesantias' => $cesantiaIds[0],
                'cod_caja_compensacion' => $cajaIds[0],
                'cod_empleado' => $empleado1->cod_empleado,
                'descripcion' => 'Afiliacion integral empleado operaciones.',
                'tipo_regimen' => 'Contributivo',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'fecha_afiliacion_eps' => '2024-03-01',
                'fecha_afiliacion_arl' => '2024-03-01',
                'fecha_afiliacion_caja' => '2024-03-01',
                'fecha_afiliacion_fondo_pensiones' => '2024-03-01',
                'fecha_afiliacion_fondo_cesantias' => '2024-03-01',
                'estado_afiliacion' => 'Activa',
                'cod_eps' => $epsIds[1] ?? $epsIds[0],
                'cod_arl' => $arlIds[1] ?? $arlIds[0],
                'cod_riesgo' => $riesgoIds[1] ?? $riesgoIds[0],
                'cod_fondo_pensiones' => $pensionIds[1] ?? $pensionIds[0],
                'cod_fondo_cesantias' => $cesantiaIds[1] ?? $cesantiaIds[0],
                'cod_caja_compensacion' => $cajaIds[1] ?? $cajaIds[0],
                'cod_empleado' => $empleado2->cod_empleado,
                'descripcion' => 'Afiliacion integral empleada financiera.',
                'tipo_regimen' => 'Contributivo',
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ];

        foreach ($afiliaciones as $data) {
            DB::table('afiliaciones')->updateOrInsert(
                ['cod_empleado' => $data['cod_empleado']],
                $data
            );
        }
    }
}
