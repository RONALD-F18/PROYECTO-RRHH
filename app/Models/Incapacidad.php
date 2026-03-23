<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Incapacidad extends Model
{
    use HasFactory;

    protected $table = 'incapacidad';
    protected $primaryKey = 'cod_incapacidad';

    protected $fillable = [
        'descripcion',
        'fecha_inicio',
        'fecha_fin',
        'fecha_radicacion',
        'cod_tipo_incapacidad',
        'cod_empleado',
        'cod_clasificacion_enfermedad',
        'estado_incapacidad',
        'entidad_responsable',
    ];

    protected $casts = [
        'fecha_inicio' => 'date',
        'fecha_fin' => 'date',
        'fecha_radicacion' => 'date',
    ];

    public function tipoIncapacidad()
    {
        return $this->belongsTo(TipoIncapacidad::class, 'cod_tipo_incapacidad', 'cod_tipo_incapacidad');
    }

    public function empleado()
    {
        return $this->belongsTo(Empleado::class, 'cod_empleado', 'cod_empleado');
    }

    public function clasificacionEnfermedad()
    {
        return $this->belongsTo(ClasificacionEnfermedad::class, 'cod_clasificacion_enfermedad', 'cod_clasificacion_enfermedad');
    }
}
