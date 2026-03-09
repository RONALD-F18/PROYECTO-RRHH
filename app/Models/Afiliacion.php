<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Afiliacion extends Model
{
    protected $table = 'afiliaciones';

    protected $primaryKey = 'cod_afi';

    protected $fillable = [
        'cod_arl',
        'cod_fondo_pensiones',
        'cod_fondo_cesantias',
        'cod_caja_compensacion',
        'cod_eps',
        'riesgos',
        'estado_afiliacion',
        'tipo_regimen',
        'descripcion'
    ];
}