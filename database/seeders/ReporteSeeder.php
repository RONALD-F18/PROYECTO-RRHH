<?php

namespace Database\Seeders;

use App\Models\Reporte;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class ReporteSeeder extends Seeder
{
    public function run(): void
    {
        $usuario = DB::table('usuarios')
            ->join('roles', 'usuarios.cod_rol', '=', 'roles.cod_rol')
            ->where('roles.nombre_rol', 'funcionario')
            ->orderBy('usuarios.cod_usuario')
            ->select('usuarios.*')
            ->first()
            ?? DB::table('usuarios')->orderBy('cod_usuario')->first();

        if (! $usuario) {
            return;
        }

        $reportes = [
            [
                'modulo' => 'empleados',
                'tipo_reporte' => 'general',
                'tipo_certificacion' => 'Resumen general empleados',
                'fecha_emision' => now()->subDays(10)->toDateString(),
                'descripcion' => 'Reporte consolidado de estados y profesiones de empleados.',
            ],
            [
                'modulo' => 'contratos',
                'tipo_reporte' => 'general',
                'tipo_certificacion' => 'Resumen general contratos',
                'fecha_emision' => now()->subDays(7)->toDateString(),
                'descripcion' => 'Contratos vigentes, terminados y suspendidos por tipo.',
            ],
            [
                'modulo' => 'prestaciones',
                'tipo_reporte' => 'general',
                'tipo_certificacion' => 'Resumen prestaciones sociales',
                'fecha_emision' => now()->subDays(5)->toDateString(),
                'descripcion' => 'Totales globales de cesantias, intereses, prima y vacaciones.',
            ],
            [
                'modulo' => 'incapacidades',
                'tipo_reporte' => 'general',
                'tipo_certificacion' => 'Resumen incapacidades',
                'fecha_emision' => now()->subDays(3)->toDateString(),
                'descripcion' => 'Distribucion por tipo normativo y entidad responsable.',
            ],
            [
                'modulo' => 'disciplinario',
                'tipo_reporte' => 'general',
                'tipo_certificacion' => 'Resumen disciplinario',
                'fecha_emision' => now()->toDateString(),
                'descripcion' => 'Totales por tipo de comunicacion disciplinaria y estado.',
            ],
        ];

        foreach ($reportes as $data) {
            Reporte::query()->firstOrCreate(
                [
                    'cod_usuario' => $usuario->cod_usuario,
                    'modulo' => $data['modulo'],
                    'tipo_reporte' => $data['tipo_reporte'],
                ],
                [
                    'cod_empleado' => null,
                    'cod_contrato' => null,
                    'tipo_certificacion' => $data['tipo_certificacion'],
                    'fecha_emision' => $data['fecha_emision'],
                    'descripcion' => $data['descripcion'],
                    'estado' => 'Generado',
                ]
            );
        }
    }
}
