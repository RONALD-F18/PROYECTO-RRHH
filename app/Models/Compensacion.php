<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Compensacion extends Model
{
    use HasFactory;

    protected $table = 'caja_compensaciones';
    protected $primaryKey = 'cod_caja_compensacion'; 

    protected $fillable = [
        'nombre',
        'nit',
        'direccion',
        'telefono',
        'email',
    ];
    

}