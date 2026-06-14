<?php

namespace App\Repositories\Eloquent;

use App\Models\Inasistencia;
use Illuminate\Database\Eloquent\Collection;
use App\Repositories\Interfaces\InasistenciaInterface;

class InasistenciaRepository implements InasistenciaInterface
{
    public function getAllInasistencias(): Collection
    {
        return Inasistencia::query()->orderBy('fecha_inasistencia', 'desc')->get();
    }

    public function buscarInasistencias(?int $codEmpleado = null, ?int $mes = null, ?int $anio = null): Collection
    {
        $query = Inasistencia::query()->orderBy('fecha_inasistencia', 'desc');

        if ($codEmpleado !== null) {
            $query->where('cod_empleado', $codEmpleado);
        }

        if ($mes !== null && $anio !== null) {
            $query->whereYear('fecha_inasistencia', $anio)
                ->whereMonth('fecha_inasistencia', $mes);
        } elseif ($anio !== null) {
            $query->whereYear('fecha_inasistencia', $anio);
        }

        return $query->get();
    }

    public function getInasistenciaById($cod_inasistencias): ?Inasistencia
    {
        $inasistencia = Inasistencia::find($cod_inasistencias);
        return !$inasistencia ? null : $inasistencia;
    }

    public function createInasistencia(array $data): Inasistencia
    {
        return Inasistencia::create($data);
    }

    public function updateInasistencia($cod_inasistencias, array $data): ?Inasistencia
    {
        $inasistencia = Inasistencia::find($cod_inasistencias);
        if (!$inasistencia) {
            return null;
        }
        $inasistencia->update($data);
        return $inasistencia;
    }

    public function deleteInasistencia($cod_inasistencias): bool
    {
        $inasistencia = Inasistencia::find($cod_inasistencias);
        if (!$inasistencia) {
            return false;
        }
        $inasistencia->delete();
        return true;
    }

    public function deleteByEmpleadoId(int $codEmpleado): int
    {
        return Inasistencia::query()->where('cod_empleado', $codEmpleado)->delete();
    }
}
