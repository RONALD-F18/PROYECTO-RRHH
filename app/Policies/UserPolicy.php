<?php

namespace App\Policies;

use App\Models\Usuario;

class UserPolicy
{
    // VER usuario
    // Admin: ve funcionarios y su propio perfil, NO a otros admins
    // Funcionario: solo su propio perfil
    public function view(?Usuario $usuarioAutenticado, Usuario $usuarioObjetivo): bool
    {
        if (!$usuarioAutenticado) return false;

        $esSuPropiaCuenta   = $usuarioAutenticado->cod_usuario    ===  $usuarioObjetivo->cod_usuario;
        $esAdminAutenticado = $usuarioAutenticado->roles->nombre_rol === 'administrador';
        $esAdminObjetivo    = $usuarioObjetivo->roles->nombre_rol    === 'administrador';

        if ($esSuPropiaCuenta) return true;
        if ($esAdminAutenticado && $esAdminObjetivo) return false;

        return $esAdminAutenticado;
    }

    // CREAR usuario
    // Solo admins crean funcionarios, los admins NUNCA se crean por API
    public function create(?Usuario $usuarioAutenticado, string $nombreRolNuevoUsuario): bool
    {
        if (!$usuarioAutenticado) return false;
        if ($nombreRolNuevoUsuario === 'administrador') return false;

        return $usuarioAutenticado->roles->nombre_rol === 'administrador';
    }

    // ACTUALIZAR usuario
    // Admin: edita funcionarios y su propio perfil, NO a otros admins
    // Funcionario: solo su propio perfil
    public function update(?Usuario $usuarioAutenticado, Usuario $usuarioObjetivo): bool
    {
        if (!$usuarioAutenticado) return false;

        $esSuPropiaCuenta   = $usuarioAutenticado->cod_usuario       === $usuarioObjetivo->cod_usuario;
        $esAdminAutenticado = $usuarioAutenticado->roles->nombre_rol  === 'administrador';
        $esAdminObjetivo    = $usuarioObjetivo->roles->nombre_rol     === 'administrador';

        if ($esSuPropiaCuenta) return true;
        if ($esAdminAutenticado && $esAdminObjetivo) return false;

        return $esAdminAutenticado;
    }

    // ELIMINAR usuario
    // Solo admins eliminan, y NUNCA a otros admins
    public function delete(?Usuario $usuarioAutenticado, Usuario $usuarioObjetivo): bool
    {
        if (!$usuarioAutenticado) return false;

        $esAdminObjetivo = $usuarioObjetivo->roles->nombre_rol === 'administrador';

        if ($esAdminObjetivo) return false;

        return $usuarioAutenticado->roles->nombre_rol === 'administrador';
    }
}