<?php

namespace App\Services;

use App\Models\Empleado;

class EmpleadoService
{
    public function getAllEmpleados()
    {
        return Empleado::all();
    }

    public function getEmpleadoById($id)
    {
        return Empleado::find($id);
    }

    public function createEmpleado(array $data)
    {
        return Empleado::create($data);
    }

    public function updateEmpleado($id, array $data)
    {
        $empleado = Empleado::find($id);

        if (!$empleado) {
            return null;
        }

        $empleado->update($data);

        return $empleado;
    }

    public function deleteEmpleado($id)
    {
        $empleado = Empleado::find($id);

        if (!$empleado) {
            return false;
        }

        return $empleado->delete();
    }
}