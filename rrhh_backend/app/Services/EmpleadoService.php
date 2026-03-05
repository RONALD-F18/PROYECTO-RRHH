<?php

namespace App\Services;

use App\Models\Empleado;
use App\Repositories\Interfaces\EmpleadoInterface;

class EmpleadoService
{
    protected EmpleadoInterface $empleadoRepository;

    public function __construct(EmpleadoInterface $empleadoRepository)
    {
        $this->empleadoRepository = $empleadoRepository;
    }

    public function GetAllEmpleados()
    {
        return $this->empleadoRepository->GetAllEmpleados();
    }

    public function GetEmpleadoById($id)
    {
        return $this->empleadoRepository->GetEmpleadoById($id);
    }

    public function CreateEmpleado(array $data)
    {
        return $this->empleadoRepository->CreateEmpleado($data);
    }

    public function UpdateEmpleado($id, array $data)
    {
        return $this->empleadoRepository->UpdateEmpleado($id, $data);
    }

    public function DeleteEmpleado($id)
    {
        return $this->empleadoRepository->DeleteEmpleado($id);
    }
}