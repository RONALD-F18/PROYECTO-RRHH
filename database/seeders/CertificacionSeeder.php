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

        if (! $empresa || ! $empleado1 || ! $empleado2) {
            return;
        }

        $contrato1 = DB::table('contrato')->where('cod_empleado', $empleado1->cod_empleado)->orderBy('cod_contrato')->first();
        $contrato2 = DB::table('contrato')->where('cod_empleado', $empleado2->cod_empleado)->orderBy('cod_contrato')->first();

        $afiliacion1 = DB::table('afiliaciones')->where('cod_empleado', $empleado1->cod_empleado)->orderByDesc('cod_afiliacion')->first();
        $afiliacion2 = DB::table('afiliaciones')->where('cod_empleado', $empleado2->cod_empleado)->orderByDesc('cod_afiliacion')->first();

        $certificaciones = [
            [
                'clave' => 'laboral-carlos',
                'id_empresa' => $empresa->id_empresa,
                'cod_empleado' => $empleado1->cod_empleado,
                'cod_contrato' => $contrato1->cod_contrato ?? null,
                'tipo_certificacion' => 'LABORAL',
                'incluye_salario' => true,
                'salario_certificado' => 2800000,
                'cod_eps' => null,
                'cod_arl' => null,
                'cod_pension' => null,
                'cod_caja' => null,
                'cod_cesantias' => null,
                'fecha_emision' => now()->subDays(20)->toDateString(),
                'ciudad_emision' => 'Medellin',
                'descripcion' => 'Certificacion laboral para tramite bancario.',
            ],
            [
                'clave' => 'laboral-ana',
                'id_empresa' => $empresa->id_empresa,
                'cod_empleado' => $empleado2->cod_empleado,
                'cod_contrato' => $contrato2->cod_contrato ?? null,
                'tipo_certificacion' => 'LABORAL',
                'incluye_salario' => false,
                'salario_certificado' => null,
                'cod_eps' => null,
                'cod_arl' => null,
                'cod_pension' => null,
                'cod_caja' => null,
                'cod_cesantias' => null,
                'fecha_emision' => now()->subDays(12)->toDateString(),
                'ciudad_emision' => 'Bogota D.C.',
                'descripcion' => 'Certificacion laboral para tramite de vivienda.',
            ],
            [
                'clave' => 'afiliaciones-carlos',
                'id_empresa' => $empresa->id_empresa,
                'cod_empleado' => $empleado1->cod_empleado,
                'cod_contrato' => $contrato1->cod_contrato ?? null,
                'tipo_certificacion' => 'AFILIACIONES',
                'incluye_salario' => false,
                'salario_certificado' => null,
                'cod_eps' => $afiliacion1->cod_eps ?? null,
                'cod_arl' => $afiliacion1->cod_arl ?? null,
                'cod_pension' => $afiliacion1->cod_fondo_pensiones ?? null,
                'cod_caja' => $afiliacion1->cod_caja_compensacion ?? null,
                'cod_cesantias' => $afiliacion1->cod_fondo_cesantias ?? null,
                'fecha_emision' => now()->subDays(8)->toDateString(),
                'ciudad_emision' => 'Medellin',
                'descripcion' => 'Constancia de afiliaciones integrales del empleado.',
            ],
            [
                'clave' => 'afiliaciones-ana',
                'id_empresa' => $empresa->id_empresa,
                'cod_empleado' => $empleado2->cod_empleado,
                'cod_contrato' => $contrato2->cod_contrato ?? null,
                'tipo_certificacion' => 'AFILIACIONES',
                'incluye_salario' => false,
                'salario_certificado' => null,
                'cod_eps' => $afiliacion2->cod_eps ?? null,
                'cod_arl' => null,
                'cod_pension' => $afiliacion2->cod_fondo_pensiones ?? null,
                'cod_caja' => null,
                'cod_cesantias' => $afiliacion2->cod_fondo_cesantias ?? null,
                'fecha_emision' => now()->subDays(4)->toDateString(),
                'ciudad_emision' => 'Bogota D.C.',
                'descripcion' => 'Constancia parcial de afiliaciones.',
            ],
        ];

        foreach ($certificaciones as $cert) {
            $clave = $cert['clave'];
            unset($cert['clave']);
            $cert['created_at'] = now();
            $cert['updated_at'] = now();

            DB::table('certificaciones')->updateOrInsert(
                [
                    'cod_empleado' => $cert['cod_empleado'],
                    'tipo_certificacion' => $cert['tipo_certificacion'],
                    'fecha_emision' => $cert['fecha_emision'],
                    'descripcion' => $cert['descripcion'],
                ],
                $cert
            );
        }
    }
}
