<?php

namespace App\Services;

use App\Repositories\Interfaces\CesantiaInterface;

class CesantiaService
{
    protected $cesantiaRepository;

    public function __construct(CesantiaInterface $cesantiaRepository)
    {
        $this->cesantiaRepository = $cesantiaRepository;
    }

    public function getAllCesantias()
    {
        return $this->cesantiaRepository->getAllCesantias();
    }

    public function getCesantiaById($id)
    {
        return $this->cesantiaRepository->getCesantiaById($id);
    }

    public function createCesantia(array $data)
    {
        return $this->cesantiaRepository->createCesantia($data);
    }

    public function updateCesantia($id, array $data)
    {
        return $this->cesantiaRepository->updateCesantia($id, $data);
    }

    public function deleteCesantia($id)
    {
        return $this->cesantiaRepository->deleteCesantia($id);
    }
}