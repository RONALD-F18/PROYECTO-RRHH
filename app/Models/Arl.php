<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model; 


class Arl extends Model
{
    protected $table = 'arls';
    protected $primaryKey = 'cod_arl';

    protected $fillable = [
        'nombre_arl',
        'descripcion_arl',
    ];
}