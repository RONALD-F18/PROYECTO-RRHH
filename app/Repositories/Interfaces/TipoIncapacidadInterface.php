<?php

namespace App\Repositories\Interfaces;

use App\Models\TipoIncapacidad;
use Illuminate\Database\Eloquent\Collection;

interface TipoIncapacidadInterface
{
    public function getAllTiposIncapacidad(): Collection;

    public function getTipoIncapacidadById($cod_tipo_incapacidad): ?TipoIncapacidad;

    public function createTipoIncapacidad(array $data): TipoIncapacidad;

    public function updateTipoIncapacidad($cod_tipo_incapacidad, array $data): ?TipoIncapacidad;

    public function deleteTipoIncapacidad($cod_tipo_incapacidad): bool;
}
