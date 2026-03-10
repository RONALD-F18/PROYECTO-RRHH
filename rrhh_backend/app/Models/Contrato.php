<?php

namespace App\Models;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Contrato extends Model
{
    use HasFactory;

    protected $table = 'Contrato';
    protected $primaryKey = 'Cod_Contrato';
    protected $fillable = [
        'tipo_contrato',
        'Cod_empleado',
        'forma_de_pago',
        'fecha_ingreso',
        'fecha_fin',
        'salario_base',
        'Cod_cargo',
        'modalidad_trabajo',
        'horario_trabajo',
        'auxilio_transporte',
        'descripcion',
        'estado_contrato'
    ];

    public function empleado()
    {
        return $this->belongsTo(Empleado::class, 'Cod_empleado', 'Cod_empleado');
    }

    public function cargo()
    {
        return $this->belongsTo(Cargo::class, 'Cod_cargo', 'Cod_cargo');
    }
}