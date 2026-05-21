<?php
namespace App\Repositories\Interfaces;

use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Collection;

interface EmpleadoInterface
{
    public function GetAllEmpleados(): Collection;

    public function PaginateEmpleados(int $page, int $perPage): LengthAwarePaginator;
    public function GetEmpleadoById($id);
    public function CreateEmpleado(array $data);
    public function UpdateEmpleado($id, array $data);
    public function DeleteEmpleado($id);
}
