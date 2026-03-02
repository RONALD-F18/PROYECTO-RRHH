<?php

namespace App\Repositories\Eloquent;

use App\Models\Rol;
use App\Repositories\Interfaces\RolInterface;

class RolRepository implements RolInterface
{
    public function getAllRoles()
    {
        $roles = Rol::all();
        return $roles;
    }

    public function getRoleById($id)
    {
        $rol = Rol::find($id);
        return !$rol ? null : $rol;
    }

    public function createRole(array $data)
    {
        $rol = Rol::create($data);
        return $rol;
    }

    public function updateRole($id, array $data)
    {
        $rol = Rol::find($id);
        if (!$rol) {
            return null;
        }
        $rol->update($data);
        return $rol;
    }

    public function deleteRole($id)
    {
        $rol = Rol::find($id);
        if (!$rol) {
            return null;
        }
        $rol->delete();
        return $rol;
    }
}