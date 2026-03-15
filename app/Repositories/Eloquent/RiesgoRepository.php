<?php

namespace App\Repositories\Eloquent;

use App\Models\Riesgo;
use App\Repositories\Interfaces\RiesgoInterface;

class RiesgoRepository implements RiesgoInterface
{
    public function getAllRiesgos()
    {
        $riesgos = Riesgo::all();
        return $riesgos;
    }

    public function getRiesgoById($id)
    {
        $riesgo = Riesgo::find($id);
        return !$riesgo ? null : $riesgo;
    }

    public function createRiesgo(array $data)
    {
        $riesgo = Riesgo::create($data);
        return $riesgo;
    }

    public function updateRiesgo($id, array $data)
    {
        $riesgo = Riesgo::find($id);
        if (!$riesgo) {
            return null;
        }
        $riesgo->update($data);
        return $riesgo;
    }

    public function deleteRiesgo($id)
    {
        $riesgo = Riesgo::find($id);
        if (!$riesgo) {
            return null;
        }
        $riesgo->delete();
        return $riesgo;
    }
}   