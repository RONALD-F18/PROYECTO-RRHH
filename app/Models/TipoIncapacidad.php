<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TipoIncapacidad extends Model
{
    use HasFactory;

    protected $table = 'tipo_incapacidad';
    protected $primaryKey = 'cod_tipo_incapacidad';

    protected $fillable = [
        'nombre_tipo',
        'descripcion',
        'clave_normativa',
    ];

    public function incapacidades()
    {
        return $this->hasMany(Incapacidad::class, 'cod_tipo_incapacidad', 'cod_tipo_incapacidad');
    }
}
