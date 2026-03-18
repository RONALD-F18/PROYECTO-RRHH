<?php

namespace App\Repositories\Eloquent;

use App\Models\ComunicacionDisciplinaria;
use Illuminate\Database\Eloquent\Collection;
use App\Repositories\Interfaces\ComunicacionDisciplinariaInterface;

class ComunicacionDisciplinariaRepository implements ComunicacionDisciplinariaInterface
{
    public function getAllComunicacionesDisciplinarias(): Collection
    {
        return ComunicacionDisciplinaria::all();
    }

    public function getComunicacionDisciplinariaById($id): ?ComunicacionDisciplinaria
    {
        return ComunicacionDisciplinaria::find($id);
    }

    public function createComunicacionDisciplinaria(array $data): ComunicacionDisciplinaria
    {
        return ComunicacionDisciplinaria::create($data);
    }

    public function updateComunicacionDisciplinaria($id, array $data): ?ComunicacionDisciplinaria
    {
        $comunicacion = ComunicacionDisciplinaria::find($id);
        if (!$comunicacion) {
            return null;
        }
        $comunicacion->update($data);
        return $comunicacion;
    }

    public function deleteComunicacionDisciplinaria($id): bool
    {
        $comunicacion = ComunicacionDisciplinaria::find($id);
        if (!$comunicacion) {
            return false;
        }
        $comunicacion->delete();
        return true;
    }
}

