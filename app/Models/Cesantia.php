<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Cesantia extends Model
{
    use HasFactory;

    protected $table = 'fondo_cesantias';
    protected $primaryKey = 'cod_fondo_cesantia'; 

    protected $fillable = [
        'nombre_fondo_cesantia',
        'descripcion_fondo_cesantia',
    ];  
}
