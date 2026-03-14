<?php

namespace App\Models;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Cargo extends Model
{
    use HasFactory;

    protected $table = 'cargo';
    protected $primaryKey = 'cod_cargo';
    protected $fillable = [
        'nomb_cargo',
        'descripcion',
    ];

    public function contratos()
    {
        return $this->hasMany(Contrato::class, 'cod_cargo', 'cod_cargo');
    }
}