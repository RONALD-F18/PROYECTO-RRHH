<?php

namespace App\Services;

use App\Repositories\Interfaces\ActividadCalendarioInterface;

class ActividadCalendarioService
{
    protected $actividadCalendarioRepository;

    public function __construct(ActividadCalendarioInterface $actividadCalendarioRepository)
    {
        $this->actividadCalendarioRepository = $actividadCalendarioRepository;
    }

    public function getAllActividadesCalendario()
    {
        return $this->actividadCalendarioRepository->getAllActividadesCalendario();
    }

    public function getActividadCalendarioById($id)
    {
        return $this->actividadCalendarioRepository->getActividadCalendarioById($id);
    }

    public function createActividadCalendario(array $data)
    {
        return $this->actividadCalendarioRepository->createActividadCalendario($data);
    }

    public function updateActividadCalendario($id, array $data)
    {
        return $this->actividadCalendarioRepository->updateActividadCalendario($id, $data);
    }

    public function deleteActividadCalendario($id)
    {
        return $this->actividadCalendarioRepository->deleteActividadCalendario($id);
    }
}

