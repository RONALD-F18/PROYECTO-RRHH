<?php

namespace App\Repositories\Eloquent;

use App\Models\ClasificacionEnfermedad;
use Illuminate\Database\Eloquent\Collection;
use App\Repositories\Interfaces\ClasificacionEnfermedadInterface;

class ClasificacionEnfermedadRepository implements ClasificacionEnfermedadInterface
{
    public function getAllClasificacionesEnfermedad(): Collection
    {
        return ClasificacionEnfermedad::orderBy('codigo_cie10')->get();
    }

    public function getClasificacionEnfermedadById($cod_clasificacion_enfermedad): ?ClasificacionEnfermedad
    {
        return ClasificacionEnfermedad::find($cod_clasificacion_enfermedad);
    }

    public function createClasificacionEnfermedad(array $data): ClasificacionEnfermedad
    {
        return ClasificacionEnfermedad::create($data);
    }

    public function updateClasificacionEnfermedad($cod_clasificacion_enfermedad, array $data): ?ClasificacionEnfermedad
    {
        $clasif = ClasificacionEnfermedad::find($cod_clasificacion_enfermedad);
        if (!$clasif) {
            return null;
        }
        $clasif->update($data);
        return $clasif;
    }

    public function deleteClasificacionEnfermedad($cod_clasificacion_enfermedad): bool
    {
        $clasif = ClasificacionEnfermedad::find($cod_clasificacion_enfermedad);
        if (!$clasif) {
            return false;
        }
        $clasif->delete();
        return true;
    }
}
