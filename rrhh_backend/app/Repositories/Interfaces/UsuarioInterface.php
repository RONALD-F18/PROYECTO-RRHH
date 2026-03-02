<?php

namespace App\Repositories\Interfaces;

interface UsuarioInterface
{                                                                                                                                                                                                                                                                                                                                                                                                                                   
    public function getAllUsuarios();
    public function getUsuarioById($id);
    public function createUsuario(array $data);
    public function updateUsuario($id, array $data);
    public function deleteUsuario($id);
}