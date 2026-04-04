<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Reporte extends Model
{
    use HasFactory;

    protected $table = 'reportes';
    protected $primaryKey = 'cod_reporte';

    protected $fillable = [
        'cod_empleado',
        'cod_contrato',
        'cod_usuario',
        'tipo_certificacion',
        'fecha_emision',
        'descripcion',
        'modulo',
        'tipo_reporte',
        'estado',
    ];

    protected $casts = [
        'fecha_emision'   => 'date',
    ];

    public function empleado()
    {
        return $this->belongsTo(Empleado::class, 'cod_empleado', 'cod_empleado');
    }

    public function contrato()
    {
        return $this->belongsTo(Contrato::class, 'cod_contrato', 'cod_contrato');
    }

    public function usuario()
    {
        return $this->belongsTo(Usuario::class, 'cod_usuario', 'cod_usuario');
    }
}

