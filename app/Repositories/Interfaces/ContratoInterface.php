<?php

namespace App\Repositories\Interfaces;

use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Collection;

interface ContratoInterface
{
    public function GetAllContratos(): Collection;

    public function PaginateContratos(int $page, int $perPage): LengthAwarePaginator;
    public function GetContratoById($id);
    public function GetContratosVigentes();
    public function GetContratoVigenteByEmpleadoId($cod_empleado);
    public function CreateContrato(array $data);
    public function UpdateContrato($id, array $data);
    public function DeleteContrato($id);
}