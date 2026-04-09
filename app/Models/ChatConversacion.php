<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class ChatConversacion extends Model
{
    protected $table = 'chat_conversaciones';

    protected $primaryKey = 'cod_chat_conversacion';

    protected $fillable = [
        'cod_usuario',
        'titulo',
    ];

    public function usuario(): BelongsTo
    {
        return $this->belongsTo(Usuario::class, 'cod_usuario', 'cod_usuario');
    }

    public function mensajes(): HasMany
    {
        return $this->hasMany(ChatMensaje::class, 'cod_chat_conversacion', 'cod_chat_conversacion')
            ->orderBy('created_at');
    }
}
