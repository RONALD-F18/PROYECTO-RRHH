<?php

namespace App\Repositories\Eloquent;

use App\Models\CalendarioActividad;
use Illuminate\Database\Eloquent\Collection;
use App\Repositories\Interfaces\CalendarioActividadesInterface;

class CalendarioActividadesRepository implements CalendarioActividadesInterface
{
    public function GetAllCalendarioActividades(): Collection
    {
        $CalendarioActividades = CalendarioActividad::all();
        return $CalendarioActividades;
    }

    public function GetCalendarioActividadById($id): ?CalendarioActividad
    {
        $CalendarioActividad = CalendarioActividad::find($id);
        return !$CalendarioActividad ? null : $CalendarioActividad;
    }

    public function CreateCalendarioActividad(array $data): CalendarioActividad
    {
        $CalendarioActividad = CalendarioActividad::create($data);
        return $CalendarioActividad;
    }

    public function UpdateCalendarioActividad($id, array $data): ?CalendarioActividad
    {
        $CalendarioActividad = CalendarioActividad::find($id);
        if (!$CalendarioActividad) {
            return null;
        }
        $CalendarioActividad->update($data);
        return $CalendarioActividad;
    }

    public function DeleteCalendarioActividad($id): bool
    {
        $CalendarioActividad = CalendarioActividad::find($id);
        if (!$CalendarioActividad) {
            return false;
        }
        $CalendarioActividad->delete();
        return true;
    }
}