<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Riesgo extends Model
{
    use HasFactory;

    protected $table = 'riesgos';
    protected $primaryKey = 'cod_riesgo';

    protected $fillable = [
        'nombre_riesgo',
        'descripcion_riesgo',
    ];

}