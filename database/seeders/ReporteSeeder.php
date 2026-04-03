<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Reporte;
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

        if (!$usuario) {
            return;
        }

        // Reporte general de empleados
        Reporte::create([
            'cod_usuario' => $usuario->cod_usuario,
            'cod_empleado' => null,
            'cod_contrato' => null,
            'tipo_certificacion' => 'Resumen general empleados',
            'fecha_emision' => now()->subDays(10)->toDateString(),
            'descripcion' => 'Reporte consolidado de estados y profesiones de empleados.',
            'modulo' => 'empleados',
            'tipo_reporte' => 'general',
            'estado' => 'Generado',
        ]);

        // Reporte general de contratos
        Reporte::create([
            'cod_usuario' => $usuario->cod_usuario,
            'cod_empleado' => null,
            'cod_contrato' => null,
            'tipo_certificacion' => 'Resumen general contratos',
            'fecha_emision' => now()->subDays(7)->toDateString(),
            'descripcion' => 'Contratos vigentes, terminados y suspendidos por tipo.',
            'modulo' => 'contratos',
            'tipo_reporte' => 'general',
            'estado' => 'Generado',
        ]);

        // Reporte general de prestaciones sociales
        Reporte::create([
            'cod_usuario' => $usuario->cod_usuario,
            'cod_empleado' => null,
            'cod_contrato' => null,
            'tipo_certificacion' => 'Resumen prestaciones sociales',
            'fecha_emision' => now()->subDays(5)->toDateString(),
            'descripcion' => 'Totales globales de cesantías, intereses, prima y vacaciones.',
            'modulo' => 'prestaciones',
            'tipo_reporte' => 'general',
            'estado' => 'Generado',
        ]);

        // Reporte general de incapacidades
        Reporte::create([
            'cod_usuario' => $usuario->cod_usuario,
            'cod_empleado' => null,
            'cod_contrato' => null,
            'tipo_certificacion' => 'Resumen incapacidades',
            'fecha_emision' => now()->subDays(3)->toDateString(),
            'descripcion' => 'Distribución por tipo normativo y entidad responsable.',
            'modulo' => 'incapacidades',
            'tipo_reporte' => 'general',
            'estado' => 'Generado',
        ]);

        // Reporte general disciplinario
        Reporte::create([
            'cod_usuario' => $usuario->cod_usuario,
            'cod_empleado' => null,
            'cod_contrato' => null,
            'tipo_certificacion' => 'Resumen disciplinario',
            'fecha_emision' => now()->toDateString(),
            'descripcion' => 'Totales por tipo de comunicación disciplinaria y estado.',
            'modulo' => 'disciplinario',
            'tipo_reporte' => 'general',
            'estado' => 'Generado',
        ]);
    }
}

