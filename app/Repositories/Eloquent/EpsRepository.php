<?php

namespace App\Repositories\Eloquent;

use App\Models\Eps;
use App\Repositories\Interfaces\EpsInterface;

class EpsRepository implements EpsInterface
{
    public function getAllEps()
    {
        $eps = Eps::all();
        return $eps;
    }

    public function getEpsById($id)
    {
        $eps = Eps::find($id);
        return !$eps ? null : $eps;
    }

    public function createEps(array $data)
    {
        $eps = Eps::create($data);
        return $eps;
    }

    public function updateEps($id, array $data)
    {
        $eps = eps::find($id);
        if (!$eps) {
            return null;
        }
        $eps->update($data);
        return $eps;
    }

    public function deleteEps($id)
    {
        $eps = Eps::find($id);
        if (!$eps) {
            return false;
        }
        $eps->delete();
        return true;
    }
}