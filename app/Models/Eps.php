<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Eps extends Model
{
    protected $table = 'eps';
    protected $primaryKey = 'cod_eps';

    protected $fillable = [
        'nombre_eps',
        'descripcion_eps',
    ];
}
