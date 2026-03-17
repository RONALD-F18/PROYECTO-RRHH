<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class CalendarioActividadSeeder extends Seeder
{
    public function run(): void
    {
        $usuarioId = 1; // Admin por defecto

        DB::table('actividades_calendario')->insert([
            [
                'titulo'             => 'Pago de nómina',
                'tipo'               => 'NÓMINA',
                'fecha_inicio'       => '2026-03-30',
                'fecha_fin'          => null,
                'estado'             => 'PENDIENTE',
                'descripcion'        => 'Proceso mensual de liquidación y pago de nómina a todos los empleados.',
                'prioridad'          => 'ALTA',
                'color'              => '#FF5722',
                'cod_usuario'        => $usuarioId,
                'fecha_creacion'     => now()->toDateString(),
                'fecha_recordatorio' => '2026-03-28',
                'created_at'         => now(),
                'updated_at'         => now(),
            ],
            [
                'titulo'             => 'Reporte SG-SST',
                'tipo'               => 'SST',
                'fecha_inicio'       => '2026-04-05',
                'fecha_fin'          => null,
                'estado'             => 'PENDIENTE',
                'descripcion'        => 'Revisión de incidentes, incapacidades y reporte al Sistema de Gestión de Seguridad y Salud en el Trabajo.',
                'prioridad'          => 'MEDIA',
                'color'              => '#4CAF50',
                'cod_usuario'        => $usuarioId,
                'fecha_creacion'     => now()->toDateString(),
                'fecha_recordatorio' => '2026-04-03',
                'created_at'         => now(),
                'updated_at'         => now(),
            ],
            [
                'titulo'             => 'Capacitación en prevención de acoso laboral',
                'tipo'               => 'CAPACITACIÓN',
                'fecha_inicio'       => '2026-04-15',
                'fecha_fin'          => '2026-04-15',
                'estado'             => 'PENDIENTE',
                'descripcion'        => 'Jornada de formación obligatoria para todos los colaboradores sobre la Ley 1010 de 2006.',
                'prioridad'          => 'MEDIA',
                'color'              => '#3F51B5',
                'cod_usuario'        => $usuarioId,
                'fecha_creacion'     => now()->toDateString(),
                'fecha_recordatorio' => '2026-04-10',
                'created_at'         => now(),
                'updated_at'         => now(),
            ],
        ]);
    }
}

