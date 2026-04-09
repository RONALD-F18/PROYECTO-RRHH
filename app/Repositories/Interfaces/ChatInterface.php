<?php

namespace App\Repositories\Interfaces;

use App\Models\ChatConversacion;
use App\Models\ChatEntradaAyuda;
use App\Models\ChatMensaje;
use Illuminate\Database\Eloquent\Collection;

interface ChatInterface
{
    public function listarConversacionesPorUsuario(int $codUsuario): Collection;

    public function obtenerConversacionDeUsuario(int $codChatConversacion, int $codUsuario): ?ChatConversacion;

    public function crearConversacion(array $data): ChatConversacion;

    public function eliminarConversacion(int $codChatConversacion, int $codUsuario): bool;

    /**
     * @return Collection<int, ChatMensaje>
     */
    public function listarMensajesDeConversacion(int $codChatConversacion, int $limite = 200): Collection;

    public function crearMensaje(array $data): ChatMensaje;

    /**
     * @param  string|null  $modulo  Si se indica, entradas de ese módulo más las de módulo "general".
     * @return Collection<int, ChatEntradaAyuda>
     */
    public function listarEntradasAyudaActivas(?string $modulo = null): Collection;

    /**
     * @return array<string, int> clave de módulo => cantidad de entradas activas
     */
    public function conteoEntradasAyudaActivasPorModulo(): array;
}
