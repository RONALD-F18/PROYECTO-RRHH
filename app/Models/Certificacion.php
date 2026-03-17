<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Certificacion extends Model
{
    use HasFactory;

    protected $table = 'certificaciones';
    protected $primaryKey = 'cod_certificacion';

    protected $fillable = [
        'id_empresa',
        'cod_empleado',
        'cod_contrato',
        'tipo_certificacion',
        'incluye_salario',
        'salario_certificado',
        'cod_eps',
        'cod_arl',
        'cod_pension',
        'cod_caja',
        'cod_cesantias',
        'fecha_emision',
        'ciudad_emision',
        'descripcion',
    ];

    public function empresa()
    {
        return $this->belongsTo(Empresa::class, 'id_empresa', 'id_empresa');
    }

    public function empleado()
    {
        return $this->belongsTo(Empleado::class, 'cod_empleado', 'cod_empleado');
    }

    public function contrato()
    {
        return $this->belongsTo(Contrato::class, 'cod_contrato', 'cod_contrato');
    }
}

