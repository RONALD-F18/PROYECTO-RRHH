<?php

namespace App\Services;

use App\Repositories\Interfaces\ComunicacionDisciplinariaInterface;

class ComunicacionDisciplinariaService
{
    protected ComunicacionDisciplinariaInterface $comunicacionRepository;

    public function __construct(ComunicacionDisciplinariaInterface $comunicacionRepository)
    {
        $this->comunicacionRepository = $comunicacionRepository;
    }

    public function getAllComunicacionesDisciplinarias()
    {
        return $this->comunicacionRepository->getAllComunicacionesDisciplinarias();
    }

    public function getComunicacionDisciplinariaById($id)
    {
        return $this->comunicacionRepository->getComunicacionDisciplinariaById($id);
    }

    public function createComunicacionDisciplinaria(array $data)
    {
        return $this->comunicacionRepository->createComunicacionDisciplinaria($data);
    }

    public function updateComunicacionDisciplinaria($id, array $data)
    {
        unset($data['cod_usuario']);

        return $this->comunicacionRepository->updateComunicacionDisciplinaria($id, $data);
    }

    public function deleteComunicacionDisciplinaria($id)
    {
        return $this->comunicacionRepository->deleteComunicacionDisciplinaria($id);
    }
}

