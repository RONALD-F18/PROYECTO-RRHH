<?php

namespace App\Repositories\Interfaces;

interface ContratoInterface
{
    public function GetAllContratos();
    public function GetContratoById($id);
    public function CreateContrato(array $data);
    public function UpdateContrato($id, array $data);
    public function DeleteContrato($id);
}