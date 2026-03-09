<?php

namespace App\Repositories\Eloquent;

use App\Models\Arl;
use App\Repositories\Interfaces\ArlInterface;

class ArlRepository implements ArlInterface
{
    public function getAllArls()
    {
        $arls = Arl::all();
        return $arls;
    }

    public function getArlById($id)
    {
        $arl = Arl::find($id);
        return $arl;
    }

    public function createArl(array $data)
    {
        $arl = Arl::create($data);
        return $arl;
    }

    public function updateArl($id, array $data)
    {
        $arl = Arl::find($id);
        if (!$arl) {
            return null;
        }

        $arl->update($data);
        return $arl;
    }

    public function deleteArl($id)
    {
        $arl = Arl::find($id);
        if (!$arl) {
            return false;
        }

        $arl->delete();
        return true;
    }
}