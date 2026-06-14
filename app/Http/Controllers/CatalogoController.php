<?php

namespace App\Http\Controllers;

use App\Models\Arl;
use App\Models\Banco;
use App\Models\Cargo;
use App\Models\Eps;

class CatalogoController extends Controller
{
    /**
     * Catálogos y enums canónicos para que el front use los mismos valores que el backend.
     */
    public function index()
    {
        return response()->json([
            'success' => true,
            'message' => 'Catálogos RRHH',
            'data' => [
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
            ],
        ]);
    }
}
