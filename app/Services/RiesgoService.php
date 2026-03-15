<?php

namespace App\Services;

use App\Repositories\Interfaces\RiesgoInterface;

class RiesgoService
{
    protected $riesgoRepository;

    public function __construct(RiesgoInterface $riesgoRepository)
    {
        $this->riesgoRepository = $riesgoRepository;
    }

    public function getAllRiesgos()
    {
        return $this->riesgoRepository->getAllRiesgos();
    }

    public function getRiesgoById($id)
    {
        return $this->riesgoRepository->getRiesgoById($id);
    }

    public function createRiesgo(array $data)
    {
        return $this->riesgoRepository->createRiesgo($data);
    }

    public function updateRiesgo($id, array $data)
    {
        return $this->riesgoRepository->updateRiesgo($id, $data);
    }

    public function deleteRiesgo($id)
    {
        return $this->riesgoRepository->deleteRiesgo($id);
    }
}   