<?php

namespace App\Services;

use App\Repositories\Interfaces\UsuarioInterface;
use Illuminate\Support\Facades\Hash;


class UsuarioService
{
    protected $usuarioRepository;

    public function __construct(UsuarioInterface $usuarioRepository)
    {
        $this->usuarioRepository = $usuarioRepository;
    }

    public function getAllUsuarios()
    {
        return $this->usuarioRepository->getAllUsuarios();
    }

    public function getUsuarioById($id)
    {
        return $this->usuarioRepository->getUsuarioById($id);
    }

    public function createUsuario(array $data)
    {

        $data['contrasena_usuario'] = Hash::make($data['contrasena_usuario']);
        return $this->usuarioRepository->createUsuario($data);
    }

    public function updateUsuario($id, array $data)
    {
        if (array_key_exists('contrasena_usuario', $data)) {
            $plain = $data['contrasena_usuario'];
            if ($plain !== null && $plain !== '') {
                $data['contrasena_usuario'] = Hash::make($plain);
            } else {
                unset($data['contrasena_usuario']);
            }
        }

        return $this->usuarioRepository->updateUsuario($id, $data);
    }

    public function deleteUsuario($id)
    {
        return $this->usuarioRepository->deleteUsuario($id);
    }
}
