<?php

namespace App\Repositories\Eloquent;

use App\Models\Cesantia;
use App\Repositories\Interfaces\CesantiaInterface;

class CesantiaRepository implements CesantiaInterface
{
    public function getAllCesantias()
    {
        $cesantias = Cesantia::all();
        return $cesantias;
    }

    public function getCesantiaById($id)
    {
        $cesantia = Cesantia::find($id);
        return !$cesantia ? null : $cesantia;
    }

    public function createCesantia(array $data)
    {
        $cesantia = Cesantia::create($data);    
        return $cesantia;
    }

    public function updateCesantia($id, array $data)
    {
        $cesantia = Cesantia::findOrFail($id);
        $cesantia->update($data);
        return $cesantia;
    }

    public function deleteCesantia($id)
    {
        $cesantia = Cesantia::findOrFail($id);
        $cesantia->delete();
        return true;
    }
}