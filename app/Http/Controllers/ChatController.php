<?php

namespace App\Http\Controllers;

use App\Http\Requests\ChatConversacionStoreRequest;
use App\Http\Requests\ChatMensajeStoreRequest;
use App\Services\ChatService;
use Illuminate\Http\Request;

class ChatController extends Controller
{
    public function __construct(
        protected ChatService $chatService
    ) {}

    /**
     * Entradas de ayuda (diccionario RRHH / módulos) sin historial.
     * Query opcional: modulo (snake_case, ej. prestaciones_sociales) para chips y lista acotada a esa pantalla.
     */
    public function ayuda(Request $request)
    {
        $modulo = $this->chatService->normalizarModuloFiltro($request->query('modulo'));
        $payload = $this->chatService->diccionarioAyudaParaCliente($modulo);

        return response()->json([
            'message' => 'Diccionario del asistente',
            'data' => $payload['entradas'],
            'sugerencias_rapidas' => $payload['sugerencias_rapidas'],
            'catalogo_modulos' => $payload['catalogo_modulos'],
            'modulo_contexto' => $payload['modulo_contexto'],
            'acciones_navegacion' => $payload['acciones_navegacion'],
            'temas_agrupados' => $payload['temas_agrupados'],
        ], 200);
    }

    /**
     * Conversaciones del usuario autenticado (JWT).
     */
    public function indexConversaciones()
    {
        $usuario = auth('api')->user();
        $data = $this->chatService->listarConversaciones((int) $usuario->cod_usuario);

        return response()->json([
            'message' => 'Conversaciones del asistente',
            'data' => $data,
        ], 200);
    }

    public function storeConversacion(ChatConversacionStoreRequest $request)
    {
        $usuario = auth('api')->user();
        $datos = $request->validated();
        $conv = $this->chatService->crearConversacion(
            (int) $usuario->cod_usuario,
            $datos['titulo'] ?? null
        );

        return response()->json([
            'message' => 'Conversación creada',
            'data' => $conv,
        ], 201);
    }

    public function destroyConversacion($cod_chat_conversacion)
    {
        $usuario = auth('api')->user();
        $ok = $this->chatService->eliminarConversacion((int) $cod_chat_conversacion, (int) $usuario->cod_usuario);
        if (! $ok) {
            return response()->json(['message' => 'Conversación no encontrada'], 404);
        }

        return response()->json(['message' => 'Conversación eliminada'], 200);
    }

    public function indexMensajes($cod_chat_conversacion)
    {
        $usuario = auth('api')->user();
        $conv = $this->chatService->obtenerConversacionDeUsuario((int) $cod_chat_conversacion, (int) $usuario->cod_usuario);
        if (! $conv) {
            return response()->json(['message' => 'Conversación no encontrada'], 404);
        }

        return response()->json([
            'message' => 'Mensajes de la conversación',
            'data' => $this->chatService->listarMensajes((int) $cod_chat_conversacion, (int) $usuario->cod_usuario),
        ], 200);
    }

    public function storeMensaje(ChatMensajeStoreRequest $request, $cod_chat_conversacion)
    {
        $usuario = auth('api')->user();
        $resultado = $this->chatService->enviarMensaje(
            (int) $usuario->cod_usuario,
            (int) $cod_chat_conversacion,
            $request->validated()['contenido']
        );

        if (! $resultado) {
            return response()->json(['message' => 'Conversación no encontrada'], 404);
        }

        return response()->json([
            'message' => 'Mensaje enviado',
            'data' => $resultado,
        ], 201);
    }
}
