<?php

namespace App\Services;
use App\Repositories\Interfaces\ContratoInterface;

class ContratoService
{
    protected $contratoRepository;

    public function __construct(ContratoInterface $contratoRepository)
    {
        $this->contratoRepository = $contratoRepository;
    }

    public function getAllContratos()
    {
        return $this->contratoRepository->getAllContratos();
    }

    public function getContratoById($id)
    {
        return $this->contratoRepository->getContratoById($id);
    }

    public function createContrato(array $data)
    {
        return $this->contratoRepository->createContrato($data);
    }

    public function updateContrato($id, array $data)
    {
        return $this->contratoRepository->updateContrato($id, $data);
    }

    public function deleteContrato($id)
    {
        return $this->contratoRepository->deleteContrato($id);
    }
}
