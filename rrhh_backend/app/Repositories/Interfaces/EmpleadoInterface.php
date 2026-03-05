<?php
namespace App\Repositories\Interfaces;

interface EmpleadoInterface
{
    public function GetAllEmpleados();
    public function GetEmpleadoById($id);
    public function CreateEmpleado(array $data);
    public function UpdateEmpleado($id, array $data);
    public function DeleteEmpleado($id);
}
