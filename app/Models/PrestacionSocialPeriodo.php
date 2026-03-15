<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PrestacionSocialPeriodo extends Model
{
    use HasFactory;

    protected $table = 'prestacion_social_periodo';
    protected $primaryKey = 'cod_prestacion_social_periodo';

    protected $fillable = [
        'cod_contrato',
        'fecha_periodo_inicio',
        'fecha_periodo_fin',
        'dias_trabajados',
        'salario_base',
        'auxilio_transporte',
        'cesantias_valor',
        'intereses_cesantias_valor',
        'prima_valor',
        'vacaciones_valor',
        'estado_pago',
        'fecha_pago_cancelacion',
        'fecha_calculo',
        'observaciones',
    ];

    protected $casts = [
        'fecha_periodo_inicio' => 'date',
        'fecha_periodo_fin' => 'date',
        'fecha_pago_cancelacion' => 'date',
        'fecha_calculo' => 'date',
        'salario_base' => 'decimal:2',
        'auxilio_transporte' => 'decimal:2',
        'cesantias_valor' => 'decimal:2',
        'intereses_cesantias_valor' => 'decimal:2',
        'prima_valor' => 'decimal:2',
        'vacaciones_valor' => 'decimal:2',
    ];

    public function contrato()
    {
        return $this->belongsTo(Contrato::class, 'cod_contrato', 'cod_contrato');
    }
}
