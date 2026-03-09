<?php

namespace App\Services;

use App\Repositories\Interfaces\AfiliacionInterface;

class AfiliacionService
{
    protected $afiliacionRepository;
    
    public function __construct(AfiliacionInterface $afiliacionRepository)
    {
        $this->afiliacionRepository = $afiliacionRepository;
    }

    public function getAllAfiliaciones()
    {
        return $this->afiliacionRepository->getAllAfiliaciones();
    }

    public function getAfiliacionById($id)
    {
        return $this->afiliacionRepository->getAfiliacionById($id);
    }

    public function createAfiliacion(array $data)
    {
        return $this->afiliacionRepository->createAfiliacion($data);
    }

    public function updateAfiliacion($id, array $data)
    {
        return $this->afiliacionRepository->updateAfiliacion($id, $data);
    }

    public function deleteAfiliacion($id)
    {
        return $this->afiliacionRepository->deleteAfiliacion($id);
    }
}