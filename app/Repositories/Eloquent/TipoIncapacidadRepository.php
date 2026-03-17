<?php

namespace App\Repositories\Eloquent;

use App\Models\TipoIncapacidad;
use Illuminate\Database\Eloquent\Collection;
use App\Repositories\Interfaces\TipoIncapacidadInterface;

class TipoIncapacidadRepository implements TipoIncapacidadInterface
{
    public function getAllTiposIncapacidad(): Collection
    {
        return TipoIncapacidad::orderBy('cod_tipo_incapacidad')->get();
    }

    public function getTipoIncapacidadById($cod_tipo_incapacidad): ?TipoIncapacidad
    {
        $tipo = TipoIncapacidad::find($cod_tipo_incapacidad);
        return !$tipo ? null : $tipo;
    }

    public function createTipoIncapacidad(array $data): TipoIncapacidad
    {
        return TipoIncapacidad::create($data);
    }

    public function updateTipoIncapacidad($cod_tipo_incapacidad, array $data): ?TipoIncapacidad
    {
        $tipo = TipoIncapacidad::find($cod_tipo_incapacidad);
        if (!$tipo) {
            return null;
        }
        $tipo->update($data);
        return $tipo;
    }

    public function deleteTipoIncapacidad($cod_tipo_incapacidad): bool
    {
        $tipo = TipoIncapacidad::find($cod_tipo_incapacidad);
        if (!$tipo) {
            return false;
        }
        $tipo->delete();
        return true;
    }
}
