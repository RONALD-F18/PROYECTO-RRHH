<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ChatDiccionarioEntrada extends Model
{
    protected $table = 'chat_diccionario_entradas';

    protected $primaryKey = 'cod_entrada';

    protected $fillable = [
        'modulo',
        'titulo',
        'contenido',
        'palabras_clave',
        'orden',
        'activo',
    ];

    protected $casts = [
        'activo' => 'boolean',
        'orden' => 'integer',
    ];
}
