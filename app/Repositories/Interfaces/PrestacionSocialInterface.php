<?php

namespace App\Repositories\Interfaces;

use App\Models\PrestacionSocialPeriodo;
use Illuminate\Database\Eloquent\Collection;

interface PrestacionSocialInterface
{
    public function getAllPrestacionesSociales(): Collection;

    public function getPrestacionSocialById($cod_prestacion_social_periodo): ?PrestacionSocialPeriodo;

    public function createPrestacionSocial(array $data): PrestacionSocialPeriodo;

    public function getTotalesPendientes(): array;

    public function getByContratoId($cod_contrato): Collection;

    public function getUltimoPeriodoByContratoId($cod_contrato): ?PrestacionSocialPeriodo;

    public function actualizarEstado($cod_prestacion_social_periodo, string $estado_pago): ?PrestacionSocialPeriodo;

    public function deletePrestacionSocial($cod_prestacion_social_periodo): bool;
}
