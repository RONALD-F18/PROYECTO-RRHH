<?php

namespace App\Repositories\Eloquent;
use App\Models\Inasistencia;
use Illuminate\Database\Eloquent\Collection;
use App\Repositories\Interfaces\Inasistenciasface;



class InasistenciaRepository implements Inasistenciasface

{
    public function getAllInasistencias(): Collection
    {
        $inasistencias = Inasistencia::all();
        return $inasistencias;
    }

    public function getInasistenciaById($cod_inasistencias): ?Inasistencia
    {
        $inasistencia = Inasistencia::find($cod_inasistencias);
        return !$inasistencia ? null : $inasistencia;
    }

    public function createInasistencia(array $data): Inasistencia
    {
        $inasistencia = Inasistencia::create($data);
        return $inasistencia;
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
}