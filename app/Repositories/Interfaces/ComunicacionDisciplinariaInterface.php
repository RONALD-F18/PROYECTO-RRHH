<?php

namespace App\Repositories\Interfaces;

use App\Models\ComunicacionDisciplinaria;
use Illuminate\Database\Eloquent\Collection;

interface ComunicacionDisciplinariaInterface
{
    public function getAllComunicacionesDisciplinarias(): Collection;

    public function getComunicacionDisciplinariaById($id): ?ComunicacionDisciplinaria;

    public function createComunicacionDisciplinaria(array $data): ComunicacionDisciplinaria;

    public function updateComunicacionDisciplinaria($id, array $data): ?ComunicacionDisciplinaria;

    public function deleteComunicacionDisciplinaria($id): bool;
}

