<?php

namespace App\Services;

use App\Repositories\Interfaces\CargoInterface;

class CargoService
{
    protected $cargoRepository;

    public function __construct(CargoInterface $cargoRepository)
    {
        $this->cargoRepository = $cargoRepository;
    }

    public function getAllCargos()
    {
        return $this->cargoRepository->getAllCargos();
    }

    public function getCargoById($id)
    {
        return $this->cargoRepository->getCargoById($id);
    }

    public function createCargo(array $data)
    {
        return $this->cargoRepository->createCargo($data);
    }

    public function updateCargo($id, array $data)
    {
        return $this->cargoRepository->updateCargo($id, $data);
    }

    public function deleteCargo($id)
    {
        return $this->cargoRepository->deleteCargo($id);
    }
}