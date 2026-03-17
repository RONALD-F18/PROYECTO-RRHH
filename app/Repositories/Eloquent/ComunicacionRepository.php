<?php

namespace App\Repositories\Eloquent;

use App\Models\Comunicacion;
use App\Repositories\Interfaces\ComunicacionInterface;

class ComunicacionRepository implements ComunicacionInterface
{
    public function GetAllComunicaciones(): Collection
    {
        $Comunicaciones = Comunicacion::all();
        return $Comunicaciones;
    }

    public function GetComunicacionById($id): ?Comunicacion
    {
        $Comunicacion = Comunicacion::find($id);
        return !$Comunicacion ? null : $Comunicacion;
    }

    public function CreateComunicacion(array $data): Comunicacion
    {
        $Comunicacion = Comunicacion::create($data);
        return $Comunicacion;
    }

    public function UpdateComunicacion($id, array $data): ?Comunicacion
    {
        $Comunicacion = Comunicacion::find($id);
        if (!$Comunicacion) {
            return null;
        }
        $Comunicacion->update($data);
        return $Comunicacion;
    }

    public function DeleteComunicacion($id): bool
    {
        $Comunicacion = Comunicacion::find($id);
        if (!$Comunicacion) {
            return false;
        }
        $Comunicacion->delete();
        return true;
    }
}
