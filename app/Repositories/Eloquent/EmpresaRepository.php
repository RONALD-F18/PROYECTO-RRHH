<?php

namespace App\Repositories\Eloquent;

use App\Models\Empresa;
use App\Repositories\Interfaces\EmpresaInterface;

class EmpresaRepository implements EmpresaInterface
{
    public function getAllEmpresas()
    {
        return Empresa::all();
    }

    public function getEmpresaById($id)
    {
        $empresa = Empresa::find($id);
        return !$empresa ? null : $empresa;
    }

    public function createEmpresa(array $data)
    {
        return Empresa::create($data);
    }

    public function updateEmpresa($id, array $data)
    {
        $empresa = Empresa::find($id);
        if (!$empresa) {
            return null;
        }

        $empresa->update($data);
        return $empresa;
    }

    public function deleteEmpresa($id)
    {
        $empresa = Empresa::find($id);
        if (!$empresa) {
            return false;
        }

        $empresa->delete();
        return true;
    }
}

