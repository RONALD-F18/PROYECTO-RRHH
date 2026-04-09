<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ChatMensaje extends Model
{
    public const ROL_USUARIO = 'usuario';

    public const ROL_ASISTENTE = 'asistente';

    protected $table = 'chat_mensajes';

    protected $primaryKey = 'cod_chat_mensaje';

    protected $fillable = [
        'cod_chat_conversacion',
        'rol',
        'contenido',
    ];

    public function conversacion(): BelongsTo
    {
        return $this->belongsTo(ChatConversacion::class, 'cod_chat_conversacion', 'cod_chat_conversacion');
    }
}
