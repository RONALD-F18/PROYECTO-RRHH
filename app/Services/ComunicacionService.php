<?php

namespace App\Services;

use App\Repositories\Interfaces\ComunicacionInterface;

class ComunicacionService
{
    protected $comunicacionRepository;

    public function __construct(ComunicacionInterface $comunicacionRepository)
    {
        $this->comunicacionRepository = $comunicacionRepository;
    }

    public function getAllComunicaciones()
    {
        return $this->comunicacionRepository->GetAllComunicaciones();
    }

    public function getComunicacionById($id)
    {
        return $this->comunicacionRepository->GetComunicacionById($id);
    }

    public function createComunicacion(array $data)
    {
        return $this->comunicacionRepository->CreateComunicacion($data);
    }

    public function updateComunicacion($id, array $data)
    {
        return $this->comunicacionRepository->UpdateComunicacion($id, $data);
    }

    public function deleteComunicacion($id)
    {
        return $this->comunicacionRepository->DeleteComunicacion($id);
    }
}