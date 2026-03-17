<?php

namespace App\Services;

use App\Repositories\Interfaces\EmpresaInterface;

class EmpresaService
{
    protected $empresaRepository;

    public function __construct(EmpresaInterface $empresaRepository)
    {
        $this->empresaRepository = $empresaRepository;
    }

    public function getAllEmpresas()
    {
        return $this->empresaRepository->getAllEmpresas();
    }

    public function getEmpresaById($id)
    {
        return $this->empresaRepository->getEmpresaById($id);
    }

    public function createEmpresa(array $data)
    {
        $data['fecha_actualizacion'] = now();
        return $this->empresaRepository->createEmpresa($data);
    }

    public function updateEmpresa($id, array $data)
    {
        $data['fecha_actualizacion'] = now();
        return $this->empresaRepository->updateEmpresa($id, $data);
    }

    public function deleteEmpresa($id)
    {
        return $this->empresaRepository->deleteEmpresa($id);
    }
}

