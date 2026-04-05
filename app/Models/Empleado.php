<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/** estado_emp canónico (API / front): ACTIVO | RETIRADO */
class Empleado extends Model
{
    use HasFactory;

    protected $table = 'empleados';
    protected $primaryKey = 'cod_empleado';
    protected $fillable = [
    'nombre_empleado',
    'apellidos_empleado',   
    'doc_iden',
    'tipo_documento',
    'fecha_nac',
    'direccion',
    'numero_telefono',
    'correo_empleado',
    'numero_cuenta',
    'tipo_cuenta',
    'cod_banco',
    'estado_emp',
    'discapacidad',
    'nacionalidad',
    'estado_civil',
    'grupo_sanguineo',
    'profesion',
    'fec_exp_doc',
    'descripcion',
    'cod_usuario',
];

    public function bancos()
    {
        return $this->belongsTo(Banco::class, 'cod_banco', 'cod_banco');
    }

    public function afiliaciones()
    {
        return $this->hasMany(Afiliacion::class, 'cod_empleado', 'cod_empleado');
    }
}