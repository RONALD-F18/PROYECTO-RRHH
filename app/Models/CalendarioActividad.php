<?php

namespace App\Models;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;


class CalendarioActividad extends Model
{
    use HasFactory;

    protected $table = 'calendario_actividades';
    protected $primaryKey = 'cod_calendario_actividad';
    protected $fillable = [
        'nomb_calendario_actividad',
        'descripcion',
        'fecha_inicio',
        'fecha_fin',
    ];

    public function actividades()
    {
        return $this->hasMany(Actividad::class, 'cod_calendario_actividad', 'cod_calendario_actividad');
    }
}
