<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Usuario extends Model
{
    use HasFactory;

    protected $table = 'usuarios';
    protected $primaryKey = 'cod_usuario';
    protected $fillable = [
        'nombre_usuario',
        'email_usuario',
        'contrasena_usuario',
        'cod_rol',
        'estado_usuario',
        'fecha_registro',
    ];

    public function usuarios(){
        return $this->belongsTo(Rol::class, 'cod_rol', 'cod_rol');
    }
}
