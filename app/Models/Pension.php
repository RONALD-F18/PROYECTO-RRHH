<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;  

class Pension extends Model
{
    use HasFactory;

    protected $table = 'fondo_pensiones';
    protected $primaryKey = 'cod_fondo_pension';

    protected $fillable = [
        'nombre_fondo_pension',
        'descripcion_fondo_pension',
    ];

    public function afiliaciones()
    {
        return $this->hasMany(Afiliacion::class, 'cod_fondo_pension', 'cod_fondo_pension');
    }

}