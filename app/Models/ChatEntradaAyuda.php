<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ChatEntradaAyuda extends Model
{
    protected $table = 'chat_entradas_ayuda';

    protected $primaryKey = 'cod_entrada_ayuda';

    protected $fillable = [
        'titulo',
        'modulo',
        'palabras_clave',
        'contenido',
        'orden',
        'activo',
    ];

    protected $casts = [
        'activo' => 'boolean',
        'orden' => 'integer',
    ];
}
