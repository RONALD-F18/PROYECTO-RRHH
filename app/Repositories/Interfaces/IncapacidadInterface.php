<?php

namespace App\Repositories\Interfaces;

use App\Models\Incapacidad;
use Illuminate\Database\Eloquent\Collection;

interface IncapacidadInterface
{
    public function getAllIncapacidades(): Collection;

    public function getIncapacidadById($cod_incapacidad): ?Incapacidad;

    public function createIncapacidad(array $data): Incapacidad;

    public function updateIncapacidad($cod_incapacidad, array $data): ?Incapacidad;

    public function deleteIncapacidad($cod_incapacidad): bool;

    public function getByEmpleadoId($cod_empleado): Collection;
}
