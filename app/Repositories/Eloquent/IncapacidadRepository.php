<?php

namespace App\Repositories\Eloquent;

use App\Models\Incapacidad;
use Illuminate\Database\Eloquent\Collection;
use App\Repositories\Interfaces\IncapacidadInterface;

class IncapacidadRepository implements IncapacidadInterface
{
    public function getAllIncapacidades(): Collection
    {
        return Incapacidad::with('tipoIncapacidad', 'empleado', 'clasificacionEnfermedad')
            ->orderBy('fecha_inicio', 'desc')
            ->get();
    }

    public function getIncapacidadById($cod_incapacidad): ?Incapacidad
    {
        $incapacidad = Incapacidad::with('tipoIncapacidad', 'empleado', 'clasificacionEnfermedad')
            ->find($cod_incapacidad);
        return !$incapacidad ? null : $incapacidad;
    }

    public function createIncapacidad(array $data): Incapacidad
    {
        return Incapacidad::create($data);
    }

    public function updateIncapacidad($cod_incapacidad, array $data): ?Incapacidad
    {
        $incapacidad = Incapacidad::find($cod_incapacidad);
        if (!$incapacidad) {
            return null;
        }
        $incapacidad->update($data);
        return $incapacidad;
    }

    public function deleteIncapacidad($cod_incapacidad): bool
    {
        $incapacidad = Incapacidad::find($cod_incapacidad);
        if (!$incapacidad) {
            return false;
        }
        $incapacidad->delete();
        return true;
    }

    public function getByEmpleadoId($cod_empleado): Collection
    {
        return Incapacidad::with('tipoIncapacidad', 'clasificacionEnfermedad')
            ->where('cod_empleado', $cod_empleado)
            ->orderBy('fecha_inicio', 'desc')
            ->get();
    }
}
