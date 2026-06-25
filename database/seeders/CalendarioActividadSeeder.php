<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class CalendarioActividadSeeder extends Seeder
{
    public function run(): void
    {
        $usuarioId = DB::table('usuarios')->orderBy('cod_usuario')->value('cod_usuario') ?? 1;

        $actividades = [
            [
                'titulo' => 'Pago de nomina',
                'tipo' => 'NOMINA',
                'fecha_inicio' => '2026-03-30',
                'fecha_fin' => null,
                'estado' => 'PENDIENTE',
                'descripcion' => 'Proceso mensual de liquidacion y pago de nomina.',
                'prioridad' => 'ALTA',
                'color' => '#FF5722',
                'cod_usuario' => $usuarioId,
                'fecha_creacion' => now()->toDateString(),
                'fecha_recordatorio' => '2026-03-28',
            ],
            [
                'titulo' => 'Reporte SG-SST',
                'tipo' => 'SST',
                'fecha_inicio' => '2026-04-05',
                'fecha_fin' => null,
                'estado' => 'PENDIENTE',
                'descripcion' => 'Revision de incidentes e incapacidades.',
                'prioridad' => 'MEDIA',
                'color' => '#4CAF50',
                'cod_usuario' => $usuarioId,
                'fecha_creacion' => now()->toDateString(),
                'fecha_recordatorio' => '2026-04-03',
            ],
            [
                'titulo' => 'Capacitacion acoso laboral',
                'tipo' => 'CAPACITACION',
                'fecha_inicio' => '2026-04-15',
                'fecha_fin' => '2026-04-15',
                'estado' => 'PENDIENTE',
                'descripcion' => 'Formacion Ley 1010 de 2006.',
                'prioridad' => 'MEDIA',
                'color' => '#3F51B5',
                'cod_usuario' => $usuarioId,
                'fecha_creacion' => now()->toDateString(),
                'fecha_recordatorio' => '2026-04-10',
            ],
        ];

        foreach ($actividades as $data) {
            $data['created_at'] = now();
            $data['updated_at'] = now();

            DB::table('actividades_calendario')->updateOrInsert(
                ['titulo' => $data['titulo']],
                $data
            );
        }
    }
}
