<?php

namespace App\Services;

use App\Repositories\Interfaces\UsuarioInterface;

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
        return $this->usuarioRepository->createUsuario($data);
    }

    public function updateUsuario($id, array $data)
    {
        return $this->usuarioRepository->updateUsuario($id, $data);
    }

    public function deleteUsuario($id)
    {
        return $this->usuarioRepository->deleteUsuario($id);
    }
}
