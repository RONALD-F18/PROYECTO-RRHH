<?php

namespace App\Repositories\Eloquent;

use App\Models\CalendarioActividad;
use Illuminate\Database\Eloquent\Collection;
use App\Repositories\Interfaces\ActividadCalendarioInterface;

class ActividadCalendarioRepository implements ActividadCalendarioInterface
{
    public function getAllActividadesCalendario(): Collection
    {
        return CalendarioActividad::all();
    }

    public function getActividadCalendarioById($id): ?CalendarioActividad
    {
        $actividad = CalendarioActividad::find($id);
        return !$actividad ? null : $actividad;
    }

    public function createActividadCalendario(array $data): CalendarioActividad
    {
        return CalendarioActividad::create($data);
    }

    public function updateActividadCalendario($id, array $data): ?CalendarioActividad
    {
        $actividad = CalendarioActividad::find($id);
        if (!$actividad) {
            return null;
        }

        $actividad->update($data);
        return $actividad;
    }

    public function deleteActividadCalendario($id): ?CalendarioActividad
    {
        $actividad = CalendarioActividad::find($id);
        if (!$actividad) {
            return null;
        }

        $actividad->delete();
        return $actividad;
    }
}

