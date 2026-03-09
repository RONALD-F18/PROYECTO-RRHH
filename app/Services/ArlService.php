<?php

namespace App\Services;

use App\Repositories\Interfaces\ArlInterface;

class ArlService
{
    protected $arlRepository;

    public function __construct(ArlInterface $arlRepository)
    {
        $this->arlRepository = $arlRepository;
    }

    public function getAllArls()
    {
        return $this->arlRepository->getAllArls();
    }

    public function getArlById($id)
    {
        return $this->arlRepository->getArlById($id);
    }

    public function createArl(array $data)
    {
        return $this->arlRepository->createArl($data);
    }

    public function updateArl($id, array $data)
    {
        return $this->arlRepository->updateArl($id, $data);
    }

    public function deleteArl($id)
    {
        return $this->arlRepository->deleteArl($id);
    }
}