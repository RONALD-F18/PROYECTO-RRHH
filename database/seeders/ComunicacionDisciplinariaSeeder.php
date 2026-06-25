<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class ComunicacionDisciplinariaSeeder extends Seeder
{
    public function run(): void
    {
        $empleado1 = DB::table('empleados')->where('doc_iden', '7954321012')->first();
        $empleado2 = DB::table('empleados')->where('doc_iden', '5287654321')->first();
        $funcionario = DB::table('usuarios')
            ->join('roles', 'usuarios.cod_rol', '=', 'roles.cod_rol')
            ->where('roles.nombre_rol', 'funcionario')
            ->orderBy('usuarios.cod_usuario')
            ->select('usuarios.*')
            ->first();

        if (! $empleado1 || ! $empleado2 || ! $funcionario) {
            return;
        }

        $registros = [
            [
                'tipo_comunicacion'       => 'LLAMADO_VERBAL',
                'fecha_emision'           => now()->subDays(18)->toDateString(),
                'fecha_inicio_suspension' => null,
                'fecha_fin_suspension'    => null,
                'estado_comunicacion'     => 'EMITIDO',
                'motivo_comunicacion'     => 'Retraso',
                'descripcion'             => 'Llegadas tarde reiteradas en la semana; se deja constancia verbal.',
                'dias_suspension'         => null,
                'cod_empleado'            => $empleado1->cod_empleado,
                'cod_usuario'             => $funcionario->cod_usuario,
            ],
            [
                'tipo_comunicacion'       => 'MEMORANDO',
                'fecha_emision'           => now()->subDays(12)->toDateString(),
                'fecha_inicio_suspension' => now()->subDays(10)->toDateString(),
                'fecha_fin_suspension'    => now()->subDays(8)->toDateString(),
                'estado_comunicacion'     => 'NOTIFICADO',
                'motivo_comunicacion'     => 'Incumplimiento',
                'descripcion'             => 'Incumplimiento del horario de ingreso en dos ocasiones consecutivas.',
                'dias_suspension'         => 3,
                'cod_empleado'            => $empleado1->cod_empleado,
                'cod_usuario'             => $funcionario->cod_usuario,
            ],
            [
                'tipo_comunicacion'       => 'MEMORANDO',
                'fecha_emision'           => now()->subDays(7)->toDateString(),
                'fecha_inicio_suspension' => now()->subDays(5)->toDateString(),
                'fecha_fin_suspension'    => now()->subDays(5)->toDateString(),
                'estado_comunicacion'     => 'EMITIDO',
                'motivo_comunicacion'     => 'Desacato',
                'descripcion'             => 'No acato una instruccion operativa documentada por su lider inmediato.',
                'dias_suspension'         => 1,
                'cod_empleado'            => $empleado2->cod_empleado,
                'cod_usuario'             => $funcionario->cod_usuario,
            ],
            [
                'tipo_comunicacion'       => 'FELICITACION',
                'fecha_emision'           => now()->subDays(4)->toDateString(),
                'fecha_inicio_suspension' => null,
                'fecha_fin_suspension'    => null,
                'estado_comunicacion'     => 'NOTIFICADO',
                'motivo_comunicacion'     => 'Excelente trabajo',
                'descripcion'             => 'Destacado desempeno en atencion al cliente durante el mes.',
                'dias_suspension'         => null,
                'cod_empleado'            => $empleado2->cod_empleado,
                'cod_usuario'             => $funcionario->cod_usuario,
            ],
            [
                'tipo_comunicacion'       => 'FELICITACION',
                'fecha_emision'           => now()->subDays(2)->toDateString(),
                'fecha_inicio_suspension' => null,
                'fecha_fin_suspension'    => null,
                'estado_comunicacion'     => 'EMITIDO',
                'motivo_comunicacion'     => 'Conducta',
                'descripcion'             => 'Actitud proactiva y apoyo al equipo en proyecto interno.',
                'dias_suspension'         => null,
                'cod_empleado'            => $empleado1->cod_empleado,
                'cod_usuario'             => $funcionario->cod_usuario,
            ],
        ];

        foreach ($registros as $data) {
            $data['created_at'] = now();
            $data['updated_at'] = now();

            DB::table('comunicaciones_disciplinarias')->updateOrInsert(
                [
                    'cod_empleado' => $data['cod_empleado'],
                    'fecha_emision' => $data['fecha_emision'],
                    'tipo_comunicacion' => $data['tipo_comunicacion'],
                    'motivo_comunicacion' => $data['motivo_comunicacion'],
                ],
                $data
            );
        }
    }
}
