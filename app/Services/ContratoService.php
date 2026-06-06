<?php

namespace App\Services;

use App\Repositories\Interfaces\ContratoInterface;
use App\Support\PaginationMapper;

class ContratoService
{
    protected $contratoRepository;

    public function __construct(ContratoInterface $contratoRepository)
    {
        $this->contratoRepository = $contratoRepository;
    }

    public function getAllContratos()
    {
        return $this->contratoRepository->GetAllContratos();
    }

    public function paginateContratos(int $page, int $perPage): array
    {
        $paginator = $this->contratoRepository->PaginateContratos($page, $perPage);

        return PaginationMapper::toArray($paginator);
    }

    public function getContratoById($id)
    {
        return $this->contratoRepository->GetContratoById($id);
    }

    public function getContratosVigentes()
    {
        return $this->contratoRepository->GetContratosVigentes();
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
