<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Tymon\JWTAuth\Contracts\JWTSubject;

class Usuario extends Authenticatable implements JWTSubject
{
    use HasFactory;

    protected $table = 'usuarios';
    protected $primaryKey = 'cod_usuario';

    protected $fillable = [
        'nombre_usuario',
        'email_usuario',
        'contrasena_usuario',
        'cod_rol',
        'estado_usuario',
        'fecha_registro',
    ];

    protected $hidden = [
        'contrasena_usuario',
        'remember_token',
    ];

    protected $with = ['roles'];

    public function roles()
    {
        return $this->belongsTo(Rol::class, 'cod_rol', 'cod_rol');
    }

    // 🔥 MUY IMPORTANTE (le dice a Laravel cuál es el password real)
    public function getAuthPassword()
    {
        return $this->contrasena_usuario;
    }

    // JWT METHODS
    public function getJWTIdentifier()
    {
        return $this->getKey();
    }

    public function getJWTCustomClaims()
    {
        return [];
    }
}