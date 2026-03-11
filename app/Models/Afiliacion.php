<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Afiliacion extends Model
{
    protected $table = 'afiliaciones';

    protected $primaryKey = 'cod_afiliacion';

    protected $fillable = [
        'fecha_afiliacion_eps',
        'fecha_afiliacion_arl',
        'fecha_afiliacion_caja',
        'fecha_afiliacion_fondo_pensiones',
        'fecha_afiliacion_fondo_cesantias',
        'estado_afiliacion',
        'cod_eps',
        'cod_arl',
        'cod_fondo_pensiones',
        'cod_riesgo',
        'cod_fondo_cesantias',
        'cod_caja_compensacion',
        'cod_empleado',
        'descripcion',
        'tipo_regimen',
    ];

    public function eps()
    {
        return $this->belongsTo(Eps::class, 'cod_eps', 'cod_eps');
    }

    public function arl()
    {
        return $this->belongsTo(Arl::class, 'cod_arl', 'cod_arl');
    }

    public function fondo_pensiones()
    {
        return $this->belongsTo(Pension::class, 'cod_fondo_pensiones', 'cod_fondo_pensiones');
    }

    public function fondo_cesantias()
    {
        return $this->belongsTo(Cesantia::class, 'cod_fondo_cesantias', 'cod_fondo_cesantias');
    }

    public function caja_compensacion()
    {
        return $this->belongsTo(Compensacion::class, 'cod_caja_compensacion', 'cod_caja_compensacion');
    }

    public function empleado()
    {
        return $this->belongsTo(Empleado::class, 'cod_empleado', 'cod_empleado');
    }

    public function riesgo()
    {
        return $this->belongsTo(Riesgo::class, 'cod_riesgo', 'cod_riesgo');
    }
}