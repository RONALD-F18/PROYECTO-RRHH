<?php

namespace App\Services;

use App\Models\TipoIncapacidad;
use App\Repositories\Interfaces\TipoIncapacidadInterface;

class TipoIncapacidadService
{
    protected TipoIncapacidadInterface $tipoIncapacidadRepository;

    public function __construct(TipoIncapacidadInterface $tipoIncapacidadRepository)
    {
        $this->tipoIncapacidadRepository = $tipoIncapacidadRepository;
    }

    public function getAllTiposIncapacidad()
    {
        return $this->tipoIncapacidadRepository->getAllTiposIncapacidad();
    }

    public function getTipoIncapacidadById($cod_tipo_incapacidad): ?TipoIncapacidad
    {
        return $this->tipoIncapacidadRepository->getTipoIncapacidadById($cod_tipo_incapacidad);
    }

    public function createTipoIncapacidad(array $data): TipoIncapacidad
    {
        return $this->tipoIncapacidadRepository->createTipoIncapacidad($data);
    }

    public function updateTipoIncapacidad($cod_tipo_incapacidad, array $data): ?TipoIncapacidad
    {
        return $this->tipoIncapacidadRepository->updateTipoIncapacidad($cod_tipo_incapacidad, $data);
    }

    public function deleteTipoIncapacidad($cod_tipo_incapacidad): bool
    {
        return $this->tipoIncapacidadRepository->deleteTipoIncapacidad($cod_tipo_incapacidad);
    }
}
