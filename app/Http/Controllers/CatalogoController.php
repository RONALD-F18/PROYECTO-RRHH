<?php

namespace App\Http\Controllers;

use App\Models\Arl;
use App\Models\Banco;
use App\Models\Cargo;
use App\Models\Cesantia;
use App\Models\ClasificacionEnfermedad;
use App\Models\Compensacion;
use App\Models\Eps;
use App\Models\Pension;
use App\Models\Riesgo;
use App\Models\TipoIncapacidad;

class CatalogoController extends Controller
{
    public function index()
    {
        return response()->json([
            'success' => true,
            'message' => 'Catálogos RRHH',
            'data' => [
                'tipos_documento' => config('rrhh.tipos_documento'),
                'sexos_empleado' => config('rrhh.sexos_empleado'),
                'tipos_contrato' => config('rrhh.tipos_contrato'),
                'tipos_contrato_con_fecha_fin' => config('rrhh.tipos_contrato_con_fecha_fin'),
                'formas_pago' => config('rrhh.formas_pago'),
                'modalidades_trabajo' => config('rrhh.modalidades_trabajo'),
                'horarios_trabajo' => config('rrhh.horarios_trabajo'),
                'estados_incapacidad' => config('rrhh.estados_incapacidad'),
                'estados_comunicacion' => config('rrhh.estados_comunicacion'),
                'tipos_comunicacion' => config('rrhh.tipos_comunicacion'),
                'motivos_comunicacion' => config('rrhh.motivos_comunicacion'),
                'estados_afiliacion' => config('rrhh.estados_afiliacion'),
                'tipos_regimen' => config('rrhh.tipos_regimen'),
                'justificado_inasistencia' => config('rrhh.justificado_inasistencia'),
                'estados_prestacion_pago' => config('rrhh.estados_prestacion_pago'),
                'bancos' => Banco::orderBy('nombre_banco')->get(['cod_banco', 'nombre_banco']),
                'cargos' => Cargo::orderBy('nomb_cargo')->get(['cod_cargo', 'nomb_cargo']),
                'eps' => Eps::orderBy('nombre_eps')->get(['cod_eps', 'nombre_eps']),
                'arls' => Arl::orderBy('nombre_arl')->get(['cod_arl', 'nombre_arl']),
                'riesgos' => Riesgo::orderBy('nombre_riesgo')->get(['cod_riesgo', 'nombre_riesgo', 'descripcion_riesgo']),
                'fondos_pensiones' => Pension::orderBy('nombre_fondo_pension')->get(['cod_fondo_pensiones', 'nombre_fondo_pension', 'descripcion_fondo_pension']),
                'fondos_cesantias' => Cesantia::orderBy('nombre_fondo_cesantia')->get(['cod_fondo_cesantias', 'nombre_fondo_cesantia', 'descripcion_fondo_cesantia']),
                'cajas_compensacion' => Compensacion::orderBy('nombre_caja_compensacion')->get(['cod_caja_compensacion', 'nombre_caja_compensacion', 'descripcion_caja_compensacion']),
                'tipos_incapacidad' => TipoIncapacidad::orderBy('nombre_tipo')->get([
                    'cod_tipo_incapacidad',
                    'nombre_tipo',
                    'descripcion',
                    'clave_normativa',
                ]),
                'clasificaciones_enfermedad' => ClasificacionEnfermedad::orderBy('nombre_clasificacion')->get([
                    'cod_clasificacion_enfermedad',
                    'nombre_clasificacion',
                    'codigo_cie10',
                    'descripcion',
                ]),
            ],
        ]);
    }
}
