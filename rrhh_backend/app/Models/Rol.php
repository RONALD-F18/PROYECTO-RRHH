<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Rol extends Model
{
    use HasFactory;

    protected $table = 'roles';
    protected $primaryKey = 'cod_rol';
    protected $fillable = [
        'nombre_rol',
        'estado_rol',
        'descripcion',
    ];
    public function usuarios(){
        return $this->hasMany(Usuario::class, 'cod_rol', 'cod_rol');
    }
}
