<?php

namespace Database\Seeders;

use App\Models\ClasificacionEnfermedad;
use App\Models\Empleado;
use App\Models\Incapacidad;
use App\Models\TipoIncapacidad;
use Carbon\Carbon;
use Illuminate\Database\Seeder;

class IncapacidadSeeder extends Seeder
{
    public function run(): void
    {
        $carlos = Empleado::query()->where('doc_iden', '7954321012')->first();
        $ana = Empleado::query()->where('doc_iden', '5287654321')->first();

        if (! $carlos || ! $ana) {
            return;
        }

        $tipoOrigenComun = TipoIncapacidad::query()->where('clave_normativa', 'origen_comun')->first();
        $tipoLaboral = TipoIncapacidad::query()->where('clave_normativa', 'laboral')->first();
        $clasifGripa = ClasificacionEnfermedad::query()->where('codigo_cie10', 'J00')->first();
        $clasifDorsalgia = ClasificacionEnfermedad::query()->where('codigo_cie10', 'M54')->first();
        $clasifTrauma = ClasificacionEnfermedad::query()->where('codigo_cie10', 'S82')->first();

        if (! $tipoOrigenComun || ! $tipoLaboral) {
            return;
        }

        $registros = [
            [
                'cod_empleado' => $carlos->cod_empleado,
                'fecha_inicio' => '2024-06-10',
                'fecha_fin' => '2024-06-12',
                'descripcion' => 'Incapacidad por gripa',
                'cod_tipo_incapacidad' => $tipoOrigenComun->cod_tipo_incapacidad,
                'cod_clasificacion_enfermedad' => $clasifGripa?->cod_clasificacion_enfermedad,
                'estado_incapacidad' => 'Finalizada',
            ],
            [
                'cod_empleado' => $ana->cod_empleado,
                'fecha_inicio' => '2024-08-01',
                'fecha_fin' => '2024-08-05',
                'descripcion' => 'Dorsalgia',
                'cod_tipo_incapacidad' => $tipoOrigenComun->cod_tipo_incapacidad,
                'cod_clasificacion_enfermedad' => $clasifDorsalgia?->cod_clasificacion_enfermedad,
                'estado_incapacidad' => 'Finalizada',
            ],
            [
                'cod_empleado' => $carlos->cod_empleado,
                'fecha_inicio' => '2025-01-15',
                'fecha_fin' => '2025-01-20',
                'descripcion' => 'Reposo medico post consulta',
                'cod_tipo_incapacidad' => $tipoOrigenComun->cod_tipo_incapacidad,
                'cod_clasificacion_enfermedad' => $clasifGripa?->cod_clasificacion_enfermedad,
                'estado_incapacidad' => 'Activa',
            ],
            [
                'cod_empleado' => $ana->cod_empleado,
                'fecha_inicio' => '2024-11-01',
                'fecha_fin' => '2024-11-08',
                'descripcion' => 'Esguince en zona de trabajo',
                'cod_tipo_incapacidad' => $tipoLaboral->cod_tipo_incapacidad,
                'cod_clasificacion_enfermedad' => $clasifTrauma?->cod_clasificacion_enfermedad,
                'estado_incapacidad' => 'Cancelada',
            ],
        ];

        foreach ($registros as $datos) {
            Incapacidad::query()->updateOrCreate(
                [
                    'cod_empleado' => $datos['cod_empleado'],
                    'fecha_inicio' => $datos['fecha_inicio'],
                    'fecha_fin' => $datos['fecha_fin'],
                ],
                [
                    'descripcion' => $datos['descripcion'],
                    'fecha_radicacion' => Carbon::parse($datos['fecha_inicio'])->toDateString(),
                    'cod_tipo_incapacidad' => $datos['cod_tipo_incapacidad'],
                    'cod_clasificacion_enfermedad' => $datos['cod_clasificacion_enfermedad'],
                    'estado_incapacidad' => $datos['estado_incapacidad'],
                ]
            );
        }
    }
}
