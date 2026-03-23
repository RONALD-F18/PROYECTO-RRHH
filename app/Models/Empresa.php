<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Empresa extends Model
{
    use HasFactory;

    protected $table = 'empresas';
    protected $primaryKey = 'id_empresa';

    protected $fillable = [
        'nit',
        'dv',
        'razon_social',
        'nombre_comercial',
        'tipo_empresa',
        'estado_empresa',
        'fecha_constitucion',
        'direccion',
        'ciudad',
        'departamento',
        'pais',
        'telefono',
        'correo',
        'pagina_web',
        'nombre_representante',
        'documento_representante',
        'fecha_creacion',
        'fecha_actualizacion',
    ];
}

