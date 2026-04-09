<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ReporteRegistro extends Model
{
    use HasFactory;

    protected $table = 'reporte_registros';

    protected $fillable = [
        'cod_usuario',
        'modulo',
        'tipo',
        'estado',
        'descripcion',
    ];

    public function usuario(): BelongsTo
    {
        return $this->belongsTo(Usuario::class, 'cod_usuario', 'cod_usuario');
    }
}
