<?php

namespace App\Services;

use App\Repositories\Interfaces\BancoInterface;
class BancoServices
{
    protected $bancoRepository;

    public function __construct(BancoInterface $bancoRepository)
    {
        $this->bancoRepository = $bancoRepository;
    }

    public function getAllBancos()
    {
        return $this->bancoRepository->getAllBancos();
    }

    public function getBancoById($id)
    {
        return $this->bancoRepository->getBancoById($id);
    }

    public function createBanco(array $data)
    {
        return $this->bancoRepository->createBanco($data);
    }

    public function updateBanco($id, array $data)
    {
        return $this->bancoRepository->updateBanco($id, $data);
    }

    public function deleteBanco($id)
    {
        return $this->bancoRepository->deleteBanco($id);
    }
}