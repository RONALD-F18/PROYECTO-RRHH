<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class PrestacionSocialPeriodoSeeder extends Seeder
{
    public function run(): void
    {
        $contrato1 = DB::table('contrato')
            ->join('empleados', 'contrato.cod_empleado', '=', 'empleados.cod_empleado')
            ->where('empleados.doc_iden', '7954321012')
            ->select('contrato.*')
            ->first();

        $contrato2 = DB::table('contrato')
            ->join('empleados', 'contrato.cod_empleado', '=', 'empleados.cod_empleado')
            ->where('empleados.doc_iden', '5287654321')
            ->select('contrato.*')
            ->first();

        if (! $contrato1) {
            return;
        }

        $periodos = [
            [
                'cod_contrato' => $contrato1->cod_contrato,
                'fecha_periodo_inicio' => '2024-01-15',
                'fecha_periodo_fin' => '2024-03-31',
                'dias_trabajados' => 77,
                'salario_base' => 2800000,
                'auxilio_transporte' => 200000,
                'cesantias_valor' => 623333.33,
                'intereses_cesantias_valor' => 19946.67,
                'prima_valor' => 623333.33,
                'vacaciones_valor' => 299166.67,
                'estado_pago' => 'Pagado',
                'fecha_pago_cancelacion' => '2024-04-15',
                'fecha_calculo' => '2024-04-01',
                'observaciones' => 'Liquidacion primer trimestre 2024',
            ],
            [
                'cod_contrato' => $contrato1->cod_contrato,
                'fecha_periodo_inicio' => '2024-04-01',
                'fecha_periodo_fin' => '2025-12-31',
                'dias_trabajados' => 640,
                'salario_base' => 2800000,
                'auxilio_transporte' => 200000,
                'cesantias_valor' => 5333333.33,
                'intereses_cesantias_valor' => 213333.33,
                'prima_valor' => 5000000.00,
                'vacaciones_valor' => 2488888.89,
                'estado_pago' => 'Pendiente',
                'fecha_pago_cancelacion' => null,
                'fecha_calculo' => '2026-01-10',
                'observaciones' => null,
            ],
        ];

        if ($contrato2) {
            $periodos[] = [
                'cod_contrato' => $contrato2->cod_contrato,
                'fecha_periodo_inicio' => '2024-03-01',
                'fecha_periodo_fin' => '2024-06-30',
                'dias_trabajados' => 122,
                'salario_base' => 3500000,
                'auxilio_transporte' => 200000,
                'cesantias_valor' => 1502777.78,
                'intereses_cesantias_valor' => 60911.11,
                'prima_valor' => 1233333.33,
                'vacaciones_valor' => 593055.56,
                'estado_pago' => 'Pendiente',
                'fecha_pago_cancelacion' => null,
                'fecha_calculo' => '2024-07-01',
                'observaciones' => null,
            ];
        }

        foreach ($periodos as $periodo) {
            $periodo['created_at'] = now();
            $periodo['updated_at'] = now();

            DB::table('prestacion_social_periodo')->updateOrInsert(
                [
                    'cod_contrato' => $periodo['cod_contrato'],
                    'fecha_periodo_inicio' => $periodo['fecha_periodo_inicio'],
                    'fecha_periodo_fin' => $periodo['fecha_periodo_fin'],
                ],
                $periodo
            );
        }
    }
}
