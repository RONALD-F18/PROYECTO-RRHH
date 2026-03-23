<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ClasificacionEnfermedad extends Model
{
    use HasFactory;

    protected $table = 'clasificacion_enfermedad';
    protected $primaryKey = 'cod_clasificacion_enfermedad';

    protected $fillable = [
        'nombre_clasificacion',
        'codigo_cie10',
        'descripcion',
    ];

    public function incapacidades()
    {
        return $this->hasMany(Incapacidad::class, 'cod_clasificacion_enfermedad', 'cod_clasificacion_enfermedad');
    }
}
