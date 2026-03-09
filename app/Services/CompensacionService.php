<?php

namespace App\Services;

use App\Repositories\Interfaces\CompensacionInterface;  

class CompensacionService
{
    protected $compensacionRepository;

    public function __construct(CompensacionInterface $compensacionRepository)
    {
        $this->compensacionRepository = $compensacionRepository;
    }

    public function getAllCompensaciones()
    {
        return $this->compensacionRepository->getAllCompensaciones();
    }

    public function getCompensacionById($id)
    {
        return $this->compensacionRepository->getCompensacionById($id);
    }

    public function createCompensacion(array $data)
    {
        return $this->compensacionRepository->createCompensacion($data);
    }

    public function updateCompensacion($id, array $data)
    {
        return $this->compensacionRepository->updateCompensacion($id, $data);
    }

    public function deleteCompensacion($id)
    {
        return $this->compensacionRepository->deleteCompensacion($id);
    }
}   