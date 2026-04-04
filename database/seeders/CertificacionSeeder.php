<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class CertificacionSeeder extends Seeder
{
    public function run(): void
    {
        $empresa = DB::table('empresas')->orderBy('id_empresa')->first();
        $empleado1 = DB::table('empleados')->where('doc_iden', '7954321012')->first();
        $empleado2 = DB::table('empleados')->where('doc_iden', '5287654321')->first();

        if (!$empresa || !$empleado1 || !$empleado2) {
            return;
        }

        $contrato1 = DB::table('contrato')->where('cod_empleado', $empleado1->cod_empleado)->orderBy('cod_contrato')->first();
        $contrato2 = DB::table('contrato')->where('cod_empleado', $empleado2->cod_empleado)->orderBy('cod_contrato')->first();

        $afiliacion1 = DB::table('afiliaciones')->where('cod_empleado', $empleado1->cod_empleado)->orderByDesc('cod_afiliacion')->first();
        $afiliacion2 = DB::table('afiliaciones')->where('cod_empleado', $empleado2->cod_empleado)->orderByDesc('cod_afiliacion')->first();

        $a1eps = $afiliacion1->cod_eps ?? null;
        $a1arl = $afiliacion1->cod_arl ?? null;
        $a1pen = $afiliacion1->cod_fondo_pensiones ?? null;
        $a1caja = $afiliacion1->cod_caja_compensacion ?? null;
        $a1ces = $afiliacion1->cod_fondo_cesantias ?? null;
        $a2eps = $afiliacion2->cod_eps ?? null;
        $a2pen = $afiliacion2->cod_fondo_pensiones ?? null;
        $a2ces = $afiliacion2->cod_fondo_cesantias ?? null;

        DB::table('certificaciones')->insert([
            [
                'id_empresa'          => $empresa->id_empresa,
                'cod_empleado'        => $empleado1->cod_empleado,
                'cod_contrato'        => $contrato1->cod_contrato ?? null,
                'tipo_certificacion'  => 'LABORAL',
                'incluye_salario'     => true,
                'salario_certificado' => 2800000,
                'cod_eps'             => null,
                'cod_arl'             => null,
                'cod_pension'         => null,
                'cod_caja'            => null,
                'cod_cesantias'       => null,
                'fecha_emision'       => now()->subDays(20)->toDateString(),
                'ciudad_emision'      => 'Medellín',
                'descripcion'         => 'Certificación laboral para trámite bancario.',
                'created_at'          => now(),
                'updated_at'          => now(),
            ],
            [
                'id_empresa'          => $empresa->id_empresa,
                'cod_empleado'        => $empleado2->cod_empleado,
                'cod_contrato'        => $contrato2->cod_contrato ?? null,
                'tipo_certificacion'  => 'LABORAL',
                'incluye_salario'     => false,
                'salario_certificado' => null,
                'cod_eps'             => null,
                'cod_arl'             => null,
                'cod_pension'         => null,
                'cod_caja'            => null,
                'cod_cesantias'       => null,
                'fecha_emision'       => now()->subDays(12)->toDateString(),
                'ciudad_emision'      => 'Bogotá D.C.',
                'descripcion'         => 'Certificación laboral emitida para trámite de vivienda.',
                'created_at'          => now(),
                'updated_at'          => now(),
            ],
            [
                'id_empresa'          => $empresa->id_empresa,
                'cod_empleado'        => $empleado1->cod_empleado,
                'cod_contrato'        => $contrato1->cod_contrato ?? null,
                'tipo_certificacion'  => 'AFILIACIONES',
                'incluye_salario'     => false,
                'salario_certificado' => null,
                'cod_eps'             => $a1eps,
                'cod_arl'             => $a1arl,
                'cod_pension'         => $a1pen,
                'cod_caja'            => $a1caja,
                'cod_cesantias'       => $a1ces,
                'fecha_emision'       => now()->subDays(8)->toDateString(),
                'ciudad_emision'      => 'Medellín',
                'descripcion'         => 'Constancia de afiliaciones integrales del empleado.',
                'created_at'          => now(),
                'updated_at'          => now(),
            ],
            [
                'id_empresa'          => $empresa->id_empresa,
                'cod_empleado'        => $empleado2->cod_empleado,
                'cod_contrato'        => $contrato2->cod_contrato ?? null,
                'tipo_certificacion'  => 'AFILIACIONES',
                'incluye_salario'     => false,
                'salario_certificado' => null,
                'cod_eps'             => $a2eps,
                'cod_arl'             => null,
                'cod_pension'         => $a2pen,
                'cod_caja'            => null,
                'cod_cesantias'       => $a2ces,
                'fecha_emision'       => now()->subDays(4)->toDateString(),
                'ciudad_emision'      => 'Bogotá D.C.',
                'descripcion'         => 'Constancia parcial de afiliaciones para actualización de expediente.',
                'created_at'          => now(),
                'updated_at'          => now(),
            ],
        ]);
    }
}

