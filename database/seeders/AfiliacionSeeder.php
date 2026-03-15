<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use App\Models\Afiliacion;

class AfiliacionSeeder extends Seeder
{
    public function run(): void
    {
        // Usamos los primeros registros de cada tabla relacionada
        $empleados = DB::table('empleados')->orderBy('cod_empleado')->take(2)->get();

        if ($empleados->isEmpty()) {
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

        $empleado1 = $empleados[0];
        $empleado2 = $empleados[count($empleados) > 1 ? 1 : 0];

        $afiliaciones = [
            [
                'fecha_afiliacion_eps' => '2024-01-15',
                'fecha_afiliacion_arl' => '2024-01-16',
                'fecha_afiliacion_caja' => '2024-01-17',
                'fecha_afiliacion_fondo_pensiones' => '2024-01-18',
                'fecha_afiliacion_fondo_cesantias' => '2024-01-19',
                'estado_afiliacion' => 'ACTIVA',
                'cod_eps' => $epsIds[0],
                'cod_arl' => $arlIds[0],
                'cod_riesgo' => $riesgoIds[0],
                'cod_fondo_pensiones' => $pensionIds[0],
                'cod_fondo_cesantias' => $cesantiaIds[0],
                'cod_caja_compensacion' => $cajaIds[0],
                'cod_empleado' => $empleado1->cod_empleado,
                'descripcion' => 'Afiliación integral para empleado del área de operaciones.',
                'tipo_regimen' => 'CONTRIBUTIVO',
            ],
            [
                'fecha_afiliacion_eps' => '2024-02-01',
                'fecha_afiliacion_arl' => '2024-02-02',
                'fecha_afiliacion_caja' => '2024-02-03',
                'fecha_afiliacion_fondo_pensiones' => '2024-02-04',
                'fecha_afiliacion_fondo_cesantias' => '2024-02-05',
                'estado_afiliacion' => 'ACTIVA',
                'cod_eps' => $epsIds[1] ?? $epsIds[0],
                'cod_arl' => $arlIds[1] ?? $arlIds[0],
                'cod_riesgo' => $riesgoIds[1] ?? $riesgoIds[0],
                'cod_fondo_pensiones' => $pensionIds[1] ?? $pensionIds[0],
                'cod_fondo_cesantias' => $cesantiaIds[1] ?? $cesantiaIds[0],
                'cod_caja_compensacion' => $cajaIds[1] ?? $cajaIds[0],
                'cod_empleado' => $empleado2->cod_empleado,
                'descripcion' => 'Afiliación integral para empleada del área financiera.',
                'tipo_regimen' => 'CONTRIBUTIVO',
            ],
        ];

        foreach ($afiliaciones as $data) {
            Afiliacion::create($data);
        }
    }
}

