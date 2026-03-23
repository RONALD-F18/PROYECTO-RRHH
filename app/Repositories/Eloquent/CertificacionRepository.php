<?php

namespace App\Repositories\Eloquent;

use App\Models\Certificacion;
use App\Repositories\Interfaces\CertificacionInterface;

class CertificacionRepository implements CertificacionInterface
{
    public function getAll()
    {
        return Certificacion::with(['empresa', 'empleado', 'contrato'])->get();
    }

    public function getById($id)
    {
        $certificacion = Certificacion::with(['empresa', 'empleado', 'contrato'])->find($id);
        return !$certificacion ? null : $certificacion;
    }

    public function create(array $data)
    {
        return Certificacion::create($data);
    }

    public function update($id, array $data)
    {
        $certificacion = Certificacion::find($id);
        if (!$certificacion) {
            return null;
        }

        $certificacion->update($data);
        return $certificacion;
    }

    public function delete($id)
    {
        $certificacion = Certificacion::find($id);
        if (!$certificacion) {
            return false;
        }

        $certificacion->delete();
        return true;
    }
}

