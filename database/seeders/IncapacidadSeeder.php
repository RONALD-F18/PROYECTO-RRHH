<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Incapacidad;
use App\Models\Empleado;
use App\Models\TipoIncapacidad;
use App\Models\ClasificacionEnfermedad;
use Carbon\Carbon;

class IncapacidadSeeder extends Seeder
{
    public function run(): void
    {
        $empleados = Empleado::limit(5)->get();
        if ($empleados->isEmpty()) {
            return;
        }

        $tipoOrigenComun = TipoIncapacidad::where('clave_normativa', 'origen_comun')->first();
        $tipoLaboral = TipoIncapacidad::where('clave_normativa', 'laboral')->first();
        $clasif = ClasificacionEnfermedad::first();

        $base = Carbon::now()->subMonths(2);

        $registros = [
            [
                'descripcion' => 'Incapacidad por gripa',
                'fecha_inicio' => $base->copy()->addDays(5)->toDateString(),
                'fecha_fin' => $base->copy()->addDays(7)->toDateString(),
                'cod_tipo_incapacidad' => $tipoOrigenComun?->cod_tipo_incapacidad,
                'cod_clasificacion_enfermedad' => $clasif?->cod_clasificacion_enfermedad,
                'estado_incapacidad' => 'Finalizada',
            ],
            [
                'descripcion' => 'Dorsalgia',
                'fecha_inicio' => $base->copy()->addDays(20)->toDateString(),
                'fecha_fin' => $base->copy()->addDays(22)->toDateString(),
                'cod_tipo_incapacidad' => $tipoOrigenComun?->cod_tipo_incapacidad,
                'cod_clasificacion_enfermedad' => ClasificacionEnfermedad::where('codigo_cie10', 'M54')->first()?->cod_clasificacion_enfermedad,
                'estado_incapacidad' => 'Finalizada',
            ],
            [
                'descripcion' => 'Esguince en zona de trabajo',
                'fecha_inicio' => $base->copy()->addDays(40)->toDateString(),
                'fecha_fin' => $base->copy()->addDays(45)->toDateString(),
                'cod_tipo_incapacidad' => $tipoLaboral?->cod_tipo_incapacidad,
                'cod_clasificacion_enfermedad' => $clasif?->cod_clasificacion_enfermedad,
                'estado_incapacidad' => 'Finalizada',
            ],
        ];

        foreach ($registros as $i => $datos) {
            $empleado = $empleados[$i % $empleados->count()];
            if (empty($datos['cod_tipo_incapacidad'])) {
                continue;
            }
            Incapacidad::firstOrCreate(
                [
                    'cod_empleado' => $empleado->cod_empleado,
                    'fecha_inicio' => $datos['fecha_inicio'],
                    'fecha_fin' => $datos['fecha_fin'],
                ],
                [
                    'descripcion' => $datos['descripcion'],
                    'fecha_inicio' => $datos['fecha_inicio'],
                    'fecha_fin' => $datos['fecha_fin'],
                    'fecha_radicacion' => $datos['fecha_inicio'],
                    'cod_tipo_incapacidad' => $datos['cod_tipo_incapacidad'],
                    'cod_empleado' => $empleado->cod_empleado,
                    'cod_clasificacion_enfermedad' => $datos['cod_clasificacion_enfermedad'],
                    'estado_incapacidad' => $datos['estado_incapacidad'],
                ]
            );
        }
    }
}
