<?php
namespace App\Repositories\Interfaces;

use App\Models\Empleado;
use Illuminate\Database\Eloquent\Collection;


class EmpleadoRepository implements EmpleadoInterface
{
    public function GetAllEmpleados(): Collection
    {
        $Empleados = Empleado::all();
        return $Empleados;
    }

    public function GetEmpleadoById($id): ?Empleado
    {
        $Empleado = Empleado::find($id);
        return !$Empleado ? null : $Empleado;
    }

    public function CreateEmpleado(array $data): Empleado
    {
        $Empleado = Empleado::create($data);
        return $Empleado;
    }

    public function UpdateEmpleado($id, array $data): ?Empleado
    {
        $Empleado = Empleado::find($id);
        if (!$Empleado) {
            return null;
        }
        $Empleado->update($data);
        return $Empleado;
    }

    public function DeleteEmpleado($id): bool
    {
        $Empleado = Empleado::find($id);
        if (!$Empleado) {
            return false;
        }
        $Empleado->delete();
        return true;
    }


}