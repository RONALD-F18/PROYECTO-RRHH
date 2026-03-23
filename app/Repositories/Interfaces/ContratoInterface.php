<?php

namespace App\Repositories\Interfaces;

interface ContratoInterface
{
    public function GetAllContratos();
    public function GetContratoById($id);
    public function GetContratosVigentes();
    public function GetContratoVigenteByEmpleadoId($cod_empleado);
    public function CreateContrato(array $data);
    public function UpdateContrato($id, array $data);
    public function DeleteContrato($id);
}