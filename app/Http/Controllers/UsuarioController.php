<?php

namespace App\Http\Controllers;

use App\Services\UsuarioService;
use App\Http\Requests\UsuarioRequest;
use Illuminate\Support\Facades\Gate;
use Illuminate\Http\Request;

class UsuarioController extends Controller
{
    protected $usuarioService;

    public function __construct(UsuarioService $usuarioService)
    {
        $this->usuarioService = $usuarioService;
    }

    // Otros admins aparecen enmascarados en el listado
    public function index()
    {
        $userAuth = request()->user('api');
        $data     = $this->usuarioService->getAllUsuarios();

        $data = $data->map(function ($usuario) use ($userAuth) {
            $esOtroAdmin = $usuario->roles->nombre_rol === 'administrador'
                        && $usuario->cod_usuario !== $userAuth->cod_usuario;

            // Otro admin → solo nombre y rol, sin datos sensibles
            if ($esOtroAdmin) {
                return [
                    'cod_usuario' => $usuario->cod_usuario,
                    'nombre'      => $usuario->nombre_usuario,
                    'rol'         => $usuario->roles->nombre_rol,
                    'detalle'     => 'Información restringida',
                ];
            }

            return $usuario;
        });

        return response()->json([
            'success' => true,
            'message' => 'Usuarios listados correctamente',
            'data'    => $data
        ], 200);
    }

    // Policy 'view' maneja los permisos finos:
    // admin ve funcionarios y su propio perfil, NO a otros admins
    // funcionario solo ve su propio perfil
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
                'message' => 'No tienes permisos para ver este usuario'
            ], 403);
        }

        return response()->json([
            'success' => true,
            'message' => 'Usuario encontrado correctamente',
            'data'    => $usuario
        ], 200);
    }

    // Solo admins llegan aquí (middleware lo garantiza)
    // Policy 'create' bloquea crear admins por API, solo se crean funcionarios
    public function store(UsuarioRequest $request)
    {
        $codRol    = (int) $request->input('cod_rol');
        $nombreRol = \App\Models\Rol::find($codRol)?->nombre_rol ?? '';

        if (Gate::denies('create', [\App\Models\Usuario::class, $nombreRol])) {
            $mensaje = $nombreRol === 'administrador'
                ? 'Los administradores no se crean por API, usa el seeder'
                : 'No tienes permisos para crear este usuario';

            return response()->json([
                'success' => false,
                'message' => $mensaje
            ], 403);
        }

        $data = $this->usuarioService->createUsuario($request->validated());

        return response()->json([
            'success' => true,
            'message' => 'Usuario creado correctamente',
            'data'    => $data
        ], 201);
    }

    // Policy 'update' maneja los permisos finos:
    // admin edita funcionarios y su propio perfil, NO a otros admins
    // funcionario solo edita su propio perfil
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

    // Solo admins llegan aquí (middleware lo garantiza)
    // Policy 'delete' bloquea eliminar admins, solo se eliminan funcionarios
    public function destroy($id)
    {
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
