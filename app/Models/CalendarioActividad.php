<?php

namespace App\Models;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CalendarioActividad extends Model
{
    use HasFactory;

    // Tabla de actividades de calendario (tareas, reuniones, recordatorios)
    protected $table = 'actividades_calendario';
    protected $primaryKey = 'cod_actividad';

    protected $fillable = [
        'titulo',
        'tipo',
        'fecha_inicio',
        'fecha_fin',
        'estado',
        'descripcion',
        'prioridad',
        'color',
        'cod_usuario',
        'fecha_creacion',
        'fecha_recordatorio',
    ];

    /**
     * Usuario (funcionario o administrador) que creó la actividad.
     */
    public function usuario()
    {
        return $this->belongsTo(Usuario::class, 'cod_usuario', 'cod_usuario');
    }
}
