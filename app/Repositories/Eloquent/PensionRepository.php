<?php

namespace App\Repositories\Eloquent;

use App\Models\Pension;
use App\Repositories\Interfaces\PensionInterface;    

class PensionRepository implements PensionInterface
{
    public function getAll()
    {
        $pensiones = Pension::all();
        return $pensiones;
    }

    public function getPensionById($id)
    {
        $pension = Pension::find($id);
        return !$pension ? null : $pension;
    }

    public function createPension(array $data)
    {
        $pension = Pension::create($data);
        return $pension;
    }

    public function updatePension($id, array $data)
    {
        $pension = Pension::find($id);
        if (!$pension) {
            return null;
        }

        $pension->update($data);
        return $pension;
    }

    public function deletePension($id)
    {
        $pension = Pension::find($id);
        if (!$pension) {
            return false;
        }

        $pension->delete();
        return true;
    }
}