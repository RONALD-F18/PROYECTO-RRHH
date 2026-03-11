<?php

namespace App\Repositories\Eloquent;

use App\Models\Compensacion;
use App\Repositories\Interfaces\CompensacionInterface;

class CompensacionRepository implements CompensacionInterface
{
    public function getAllCompensaciones()
    {
        $compensaciones = Compensacion::all();
        return $compensaciones;
    }

    public function getCompensacionById($id)
    {
        $compensacion = Compensacion::find($id);
        return !$compensacion ? null : $compensacion;
    }

    public function createCompensacion(array $data)
    {
        $compensacion = Compensacion::create($data);
        return $compensacion;
    }

    public function updateCompensacion($id, array $data)
    {
        $compensacion = Compensacion::find($id);
        if (!$compensacion) {
            return null;
        }
        $compensacion->update($data);
        return $compensacion;
    }

    public function deleteCompensacion($id)
    {
        $compensacion = Compensacion::find($id);
        if (!$compensacion) {
            return false;
        }
        $compensacion->delete();
        return true;
    }
}