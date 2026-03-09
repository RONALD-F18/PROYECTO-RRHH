<?php

namespace App\Repositories\Eloquent;

use App\Models\Usuario;
use App\Repositories\Interfaces\UsuarioInterface;


class UsuarioRepository implements UsuarioInterface 
{
    public function getAllUsuarios()
    {
        $usuarios = Usuario::all();
        return $usuarios;
    }

    public function getUsuarioById($id)
    {
        $usuario = Usuario::find($id);
        return !$usuario ? null : $usuario;
    }

    public function createUsuario(array $data)
    {
        $usuario = Usuario::create($data);
        return $usuario;
    }

    public function updateUsuario($id, array $data)
    {
        $usuario = Usuario::find($id);
        if (!$usuario) {
            return null;
        }
        $usuario->update($data);
        return $usuario;
    }

    public function deleteUsuario($id)
    {
        $usuario = Usuario::find($id);
        if (!$usuario) {
            return null;
        }
        $usuario->delete();
        return $usuario;
    }

}