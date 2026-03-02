<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Services\UsuarioService;
use App\Http\Requests\UsuarioRequest;

class UsuarioController extends Controller
{
    protected $usuarioService;

    public function __construct(UsuarioService $usuarioService)
    {
        $this->usuarioService = $usuarioService;
    }

    public function index()
    {
        $usuarios = $this->usuarioService->getAllUsuarios();
        return response()->json($usuarios);
    }

    public function show($id)
    {
        $usuario = $this->usuarioService->getUsuarioById($id);
        if (!$usuario) {
            return response()->json(['message' => 'Usuario no encontrado'], 404);
        }
        return response()->json($usuario);
    }

    public function store(UsuarioRequest $request)
    {
        $data = $request->validated();
        $usuario = $this->usuarioService->createUsuario($data);
        return response()->json($usuario, 201);
    }

    public function update(UsuarioRequest $request, $id)
    {
        $data = $request->validated();
        $usuario = $this->usuarioService->updateUsuario($id, $data);
        if (!$usuario) {
            return response()->json(['message' => 'Usuario no encontrado'], 404);
        }
        return response()->json($usuario);
    }

    public function destroy($id)
    {
        $deleted = $this->usuarioService->deleteUsuario($id);
        if (!$deleted) {
            return response()->json(['message' => 'Usuario no encontrado'], 404);
        }
        return response()->json(['message' => 'Usuario eliminado correctamente']);
    }
}
