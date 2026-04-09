<?php

namespace App\Repositories\Eloquent;

use App\Models\ChatConversacion;
use App\Models\ChatEntradaAyuda;
use App\Models\ChatMensaje;
use App\Repositories\Interfaces\ChatInterface;
use Illuminate\Database\Eloquent\Collection;

class ChatRepository implements ChatInterface
{
    public function listarConversacionesPorUsuario(int $codUsuario): Collection
    {
        return ChatConversacion::query()
            ->where('cod_usuario', $codUsuario)
            ->orderByDesc('updated_at')
            ->get();
    }

    public function obtenerConversacionDeUsuario(int $codChatConversacion, int $codUsuario): ?ChatConversacion
    {
        return ChatConversacion::query()
            ->where('cod_chat_conversacion', $codChatConversacion)
            ->where('cod_usuario', $codUsuario)
            ->first();
    }

    public function crearConversacion(array $data): ChatConversacion
    {
        return ChatConversacion::create($data);
    }

    public function eliminarConversacion(int $codChatConversacion, int $codUsuario): bool
    {
        $c = $this->obtenerConversacionDeUsuario($codChatConversacion, $codUsuario);
        if (! $c) {
            return false;
        }

        return (bool) $c->delete();
    }

    public function listarMensajesDeConversacion(int $codChatConversacion, int $limite = 200): Collection
    {
        return ChatMensaje::query()
            ->where('cod_chat_conversacion', $codChatConversacion)
            ->orderBy('created_at')
            ->orderBy('cod_chat_mensaje')
            ->limit($limite)
            ->get();
    }

    public function crearMensaje(array $data): ChatMensaje
    {
        return ChatMensaje::create($data);
    }

    public function listarEntradasAyudaActivas(?string $modulo = null): Collection
    {
        $q = ChatEntradaAyuda::query()
            ->where('activo', true);

        if ($modulo !== null && $modulo !== '') {
            $q->where(function ($w) use ($modulo) {
                $w->where('modulo', $modulo)
                    ->orWhere('modulo', 'general');
            });
        }

        return $q->orderBy('orden')
            ->orderBy('cod_entrada_ayuda')
            ->get();
    }

    public function conteoEntradasAyudaActivasPorModulo(): array
    {
        $filas = ChatEntradaAyuda::query()
            ->where('activo', true)
            ->whereNotNull('modulo')
            ->where('modulo', '!=', '')
            ->groupBy('modulo')
            ->selectRaw('modulo, COUNT(*) as total')
            ->get();

        $out = [];
        foreach ($filas as $fila) {
            $out[$fila->modulo] = (int) $fila->total;
        }

        return $out;
    }
}
