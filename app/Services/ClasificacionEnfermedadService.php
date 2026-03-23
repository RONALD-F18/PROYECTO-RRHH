<?php

namespace App\Services;

use App\Models\ClasificacionEnfermedad;
use App\Repositories\Interfaces\ClasificacionEnfermedadInterface;

class ClasificacionEnfermedadService
{
    protected ClasificacionEnfermedadInterface $clasificacionEnfermedadRepository;

    public function __construct(ClasificacionEnfermedadInterface $clasificacionEnfermedadRepository)
    {
        $this->clasificacionEnfermedadRepository = $clasificacionEnfermedadRepository;
    }

    public function getAllClasificacionesEnfermedad()
    {
        return $this->clasificacionEnfermedadRepository->getAllClasificacionesEnfermedad();
    }

    public function getClasificacionEnfermedadById($cod_clasificacion_enfermedad): ?ClasificacionEnfermedad
    {
        return $this->clasificacionEnfermedadRepository->getClasificacionEnfermedadById($cod_clasificacion_enfermedad);
    }

    public function createClasificacionEnfermedad(array $data): ClasificacionEnfermedad
    {
        return $this->clasificacionEnfermedadRepository->createClasificacionEnfermedad($data);
    }

    public function updateClasificacionEnfermedad($cod_clasificacion_enfermedad, array $data): ?ClasificacionEnfermedad
    {
        return $this->clasificacionEnfermedadRepository->updateClasificacionEnfermedad($cod_clasificacion_enfermedad, $data);
    }

    public function deleteClasificacionEnfermedad($cod_clasificacion_enfermedad): bool
    {
        return $this->clasificacionEnfermedadRepository->deleteClasificacionEnfermedad($cod_clasificacion_enfermedad);
    }
}
