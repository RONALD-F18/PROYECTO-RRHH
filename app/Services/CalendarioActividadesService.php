<?php

namespace App\Services;

use App\Repositories\Interfaces\CalendarioActividadesInterface;

class CalendarioActividadesService
{
    protected $calendarioActividadesRepository;

    public function __construct(CalendarioActividadesInterface $calendarioActividadesRepository)
    {
        $this->calendarioActividadesRepository = $calendarioActividadesRepository;
    }

    public function getAllCalendarioActividades()
    {
        return $this->calendarioActividadesRepository->getAllCalendarioActividades();
    }

    public function getCalendarioActividadById($id)
    {
        return $this->calendarioActividadesRepository->getCalendarioActividadById($id);
    }

    public function createCalendarioActividad(array $data)
    {
        return $this->calendarioActividadesRepository->createCalendarioActividad($data);
    }

    public function updateCalendarioActividad($id, array $data)
    {
        return $this->calendarioActividadesRepository->updateCalendarioActividad($id, $data);
    }

    public function deleteCalendarioActividad($id)
    {
        return $this->calendarioActividadesRepository->deleteCalendarioActividad($id);
    }
}