<?php

namespace App\Models;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * estado_contrato canónico (API / front): ACTIVO | FINALIZADO.
 */
class Contrato extends Model
{
    use HasFactory;

    protected $table = 'contrato';
    protected $primaryKey = 'cod_contrato';
    protected $fillable = [
        'tipo_contrato',
        'cod_empleado',
        'forma_de_pago',
        'fecha_ingreso',
        'fecha_fin',
        'salario_base',
        'cod_cargo',
        'modalidad_trabajo',
        'horario_trabajo',
        'auxilio_transporte',
        'descripcion',
        'estado_contrato'
    ];

    public function empleado()
    {
        return $this->belongsTo(Empleado::class, 'cod_empleado', 'cod_empleado');
    }

    public function cargo()
    {
        return $this->belongsTo(Cargo::class, 'cod_cargo', 'cod_cargo');
    }
}