<?php

namespace App\Http\Controllers;

use App\Services\UsuarioService;
use App\Http\Requests\UsuarioRequest;
use Illuminate\Support\Facades\Gate;

class UsuarioController extends Controller
{
    protected $usuarioService;

    public function __construct(UsuarioService $usuarioService)
    {
        $this->usuarioService = $usuarioService;
    }

    // LISTAR USUARIOS — solo administrador
    public function index()
    {
        $userAuth = request()->user('api');

        if (!$userAuth) {
            return response()->json([
                'success' => false,
                'message' => 'Debes estar autenticado'
            ], 401);
        }

        if ($userAuth->roles->nombre_rol !== 'administrador') {
            return response()->json([
                'success' => false,
                'message' => 'Solo los administradores pueden listar usuarios'
            ], 403);
        }

        $data = $this->usuarioService->getAllUsuarios();

        return response()->json([
            'success' => true,
            'message' => 'Usuarios listados correctamente',
            'data'    => $data
        ], 200);
    }

    // VER USUARIO — admin ve cualquiera, otros solo a sí mismos
    public function show($id)
    {
        $usuario = $this->usuarioService->getUsuarioById($id);

        if (!$usuario) {
            return response()->json([
                'success' => false,
                'message' => 'Usuario no encontrado'
            ], 404);
        }

        if (Gate::denies('view', $usuario)) {
            return response()->json([
                'success' => false,
                'message' => 'No tienes permisos para acceder'
            ], 403);
        }

        return response()->json([
            'success' => true,
            'message' => 'Usuario encontrado correctamente',
            'data'    => $usuario
        ], 200);
    }

    // CREAR USUARIO — solo admin puede crear administradores
    public function store(UsuarioRequest $request)
    {
        $userAuth = request()->user('api');
        $codRol   = (int) $request['cod_rol'];

        if ($codRol === 1) { // 1 = administrador
            if (!$userAuth) {
                return response()->json([
                    'success' => false,
                    'message' => 'Debes estar autenticado para crear un administrador'
                ], 401);
            }

            if ($userAuth->roles->nombre_rol !== 'administrador') {
                return response()->json([
                    'success' => false,
                    'message' => 'Solo los administradores pueden crear otros administradores'
                ], 403);
            }
        }

        $data = $this->usuarioService->createUsuario($request->validated());

        return response()->json([
            'success' => true,
            'message' => 'Usuario creado correctamente',
            'data'    => $data
        ], 201);
    }

    // ACTUALIZAR USUARIO — admin a todos, otros solo a sí mismos
    public function update(UsuarioRequest $request, $id)
    {
        $usuario = $this->usuarioService->getUsuarioById($id);

        if (!$usuario) {
            return response()->json([
                'success' => false,
                'message' => 'Usuario no encontrado'
            ], 404);
        }

        if (Gate::denies('update', $usuario)) {
            return response()->json([
                'success' => false,
                'message' => 'No tienes permisos para actualizar este usuario'
            ], 403);
        }

        $updated = $this->usuarioService->updateUsuario($id, $request->validated());

        return response()->json([
            'success' => true,
            'message' => 'Usuario actualizado correctamente',
            'data'    => $updated
        ], 200);
    }

    // ELIMINAR USUARIO — solo admin
    public function destroy($id)
    {
        $userAuth = request()->user('api');

        if (!$userAuth) {
            return response()->json([
                'success' => false,
                'message' => 'Debes estar autenticado para eliminar usuarios'
            ], 401);
        }

        $usuario = $this->usuarioService->getUsuarioById($id);

        if (!$usuario) {
            return response()->json([
                'success' => false,
                'message' => 'Usuario no encontrado'
            ], 404);
        }

        if (Gate::denies('delete', $usuario)) {
            return response()->json([
                'success' => false,
                'message' => 'No tienes permisos para eliminar este usuario'
            ], 403);
        }

        $result = $this->usuarioService->deleteUsuario($id);

        if (!$result) {
            return response()->json([
                'success' => false,
                'message' => 'Error al eliminar el usuario'
            ], 500);
        }

        return response()->json([
            'success' => true,
            'message' => 'Usuario eliminado correctamente'
        ], 200);
    }
}