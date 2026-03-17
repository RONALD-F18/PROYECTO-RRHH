<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Comunicación extends Model
{
    use HasFactory;

    protected $table = 'comunicaciones_disciplinarias';
    protected $primaryKey = 'cod_disciplinario';
    protected $fillable = [
        'tipo_comunicacion',
        'fecha_emision',
        'fecha_inicio_suspension',
        'fecha_fin_suspension',
        'estado_comunicacion',
        'motivo_comunicacion',
        'descripcion',
        'dias_suspension',
        'cod_empleado',
        'cod_usuario'
    ];

    public function empleado()
    {
        return $this->belongsTo(Empleado::class, 'cod_empleado', 'cod_empleado');
    }

    public function usuario()
    {
        return $this->belongsTo(User::class, 'cod_usuario', 'id');
    }
}
