<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Inasistencia extends Model
{
    use HasFactory;

    protected $table = 'inasistencias';
    protected $primaryKey = 'cod_inasistencias';

    protected $fillable = [
        'motivo_inasistencia',
        'fecha_inasistencia',
        'cod_empleado',
        'observaciones',
        'justificado',
    ];

    public function empleado()
    {
        return $this->belongsTo(Empleado::class, 'cod_empleado', 'cod_empleado');
    }
}

