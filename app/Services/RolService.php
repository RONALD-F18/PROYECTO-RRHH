<?php

namespace App\Services;

use App\Repositories\Interfaces\RolInterface;

class RolService
{
    protected $rolRepository;

    public function __construct(RolInterface $rolRepository)
    {
        $this->rolRepository = $rolRepository;
    }

    public function getAllRoles()
    {
        return $this->rolRepository->getAllRoles();
    }

    public function getRoleById($id)
    {
        return $this->rolRepository->getRoleById($id);
    }

    public function createRole(array $data)
    {
        return $this->rolRepository->createRole($data);
    }

    public function updateRole($id, array $data)
    {
        return $this->rolRepository->updateRole($id, $data);
    }

    public function deleteRole($id)
    {
        return $this->rolRepository->deleteRole($id);
    }
}