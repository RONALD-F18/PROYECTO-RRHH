<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Banco extends Model
{
    protected $table = 'bancos';
    protected $primaryKey = 'cod_banco';
    protected $fillable = ['nombre_banco', 'descripcion_banco'];

    public function empleados()
    {
        return $this->hasMany(Empleado::class, 'cod_banco', 'cod_banco');
    }
}
