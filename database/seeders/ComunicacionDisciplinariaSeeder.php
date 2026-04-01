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
        $funcionario = DB::table('usuarios')->where('email_usuario', 'funcionario@empresa.com')->first();

        if (!$empleado1 || !$empleado2 || !$funcionario) {
            return;
        }

        DB::table('comunicaciones_disciplinarias')->insert([
            [
                'tipo_comunicacion'       => 'Llamado de atencion escrito',
                'fecha_emision'           => now()->subDays(16)->toDateString(),
                'fecha_inicio_suspension' => null,
                'fecha_fin_suspension'    => null,
                'estado_comunicacion'     => 'Cerrada',
                'motivo_comunicacion'     => 'Incumplimiento',
                'descripcion'             => 'Incumplimiento del horario de ingreso en dos ocasiones consecutivas.',
                'dias_suspension'         => null,
                'cod_empleado'            => $empleado1->cod_empleado,
                'cod_usuario'             => $funcionario->cod_usuario,
                'created_at'              => now(),
                'updated_at'              => now(),
            ],
            [
                'tipo_comunicacion'       => 'Apercibimiento formal',
                'fecha_emision'           => now()->subDays(10)->toDateString(),
                'fecha_inicio_suspension' => null,
                'fecha_fin_suspension'    => null,
                'estado_comunicacion'     => 'Emitida',
                'motivo_comunicacion'     => 'Desacato',
                'descripcion'             => 'No acato una instruccion operativa documentada por su lider inmediato.',
                'dias_suspension'         => null,
                'cod_empleado'            => $empleado2->cod_empleado,
                'cod_usuario'             => $funcionario->cod_usuario,
                'created_at'              => now(),
                'updated_at'              => now(),
            ],
            [
                'tipo_comunicacion'       => 'Suspension disciplinaria',
                'fecha_emision'           => now()->subDays(7)->toDateString(),
                'fecha_inicio_suspension' => now()->subDays(6)->toDateString(),
                'fecha_fin_suspension'    => now()->subDays(4)->toDateString(),
                'estado_comunicacion'     => 'Cerrada',
                'motivo_comunicacion'     => 'Reincidencia',
                'descripcion'             => 'Reincidencia en faltas leves luego de llamado de atencion previo.',
                'dias_suspension'         => 3,
                'cod_empleado'            => $empleado1->cod_empleado,
                'cod_usuario'             => $funcionario->cod_usuario,
                'created_at'              => now(),
                'updated_at'              => now(),
            ],
            [
                'tipo_comunicacion'       => 'Compromiso de mejora',
                'fecha_emision'           => now()->subDays(2)->toDateString(),
                'fecha_inicio_suspension' => null,
                'fecha_fin_suspension'    => null,
                'estado_comunicacion'     => 'En seguimiento',
                'motivo_comunicacion'     => 'Conducta',
                'descripcion'             => 'Se acuerda plan de mejora por comportamiento inadecuado en atencion interna.',
                'dias_suspension'         => null,
                'cod_empleado'            => $empleado2->cod_empleado,
                'cod_usuario'             => $funcionario->cod_usuario,
                'created_at'              => now(),
                'updated_at'              => now(),
            ],
        ]);
    }
}

