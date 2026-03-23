<?php

namespace App\Repositories\Interfaces;

use App\Models\ClasificacionEnfermedad;
use Illuminate\Database\Eloquent\Collection;

interface ClasificacionEnfermedadInterface
{
    public function getAllClasificacionesEnfermedad(): Collection;

    public function getClasificacionEnfermedadById($cod_clasificacion_enfermedad): ?ClasificacionEnfermedad;

    public function createClasificacionEnfermedad(array $data): ClasificacionEnfermedad;

    public function updateClasificacionEnfermedad($cod_clasificacion_enfermedad, array $data): ?ClasificacionEnfermedad;

    public function deleteClasificacionEnfermedad($cod_clasificacion_enfermedad): bool;
}
