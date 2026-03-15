<?php

namespace App\Services;

use App\Repositories\Interfaces\ArlInterface;
use App\Repositories\Interfaces\PensionInterface;

class PensionService
{
    protected $pensionRepository;

    public function __construct(PensionInterface $pensionRepository)
    {
        $this->pensionRepository = $pensionRepository;
    }

    public function getAllPensiones()
    {
        return $this->pensionRepository->getAll();
    }

    public function getPensionById($id)
    {
        return $this->pensionRepository->getPensionById($id);
    }

    public function createPension(array $data)
    {
        return $this->pensionRepository->createPension($data);
    }

    public function updatePension($id, array $data)
    {
        return $this->pensionRepository->updatePension($id, $data);
    }

    public function deletePension($id)
    {
        return $this->pensionRepository->deletePension($id);
    }
}