<?php

namespace App\Services;

use App\Repositories\Interfaces\EpsInterface;

class EpsService
{
    protected $epsRepository;

    public function __construct(EpsInterface $epsRepository)
    {
        $this->epsRepository = $epsRepository;
    }

    public function getAllEps()
    {
        return $this->epsRepository->getAllEps();
    }

    public function getEpsById($id)
    {
        return $this->epsRepository->getEpsById($id);
    }

    public function createEps(array $data)
    {
        return $this->epsRepository->createEps($data);
    }

    public function updateEps($id, array $data)
    {
        return $this->epsRepository->updateEps($id, $data);
    }

    public function deleteEps($id)
    {
        return $this->epsRepository->deleteEps($id);
    }
}