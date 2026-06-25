<?php
namespace App\Repositories\Eloquent;

use App\Models\Empleado;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Collection;
use App\Repositories\Interfaces\EmpleadoInterface;


class EmpleadoRepository implements EmpleadoInterface
{
    public function GetAllEmpleados(): Collection
    {
        return Empleado::query()->orderBy('cod_empleado')->get();
    }

    public function PaginateEmpleados(int $page, int $perPage): LengthAwarePaginator
    {
        return Empleado::query()
            ->with('bancos')
            ->orderBy('cod_empleado')
            ->paginate(perPage: $perPage, page: $page);
    }

    public function GetEmpleadoById($id): ?Empleado
    {
        $Empleado = Empleado::find($id);
        return !$Empleado ? null : $Empleado;
    }

    public function CreateEmpleado(array $data): Empleado
    {
        return Empleado::query()->create($data);
    }

    public function UpdateEmpleado($id, array $data): ?Empleado
    {
        $Empleado = Empleado::query()->find($id);
        if (! $Empleado) {
            return null;
        }
        $Empleado->update($data);

        return $Empleado->fresh();
    }

    public function DeleteEmpleado($id): bool
    {
        $Empleado = Empleado::find($id);
        if (!$Empleado) {
            return false;
        }
        $Empleado->delete();
        return true;
    }


}