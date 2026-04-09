<?php

namespace App\Repositories\Interfaces;

use App\Models\ReporteRegistro;
use App\Models\Usuario;
use Illuminate\Database\Eloquent\Collection;

interface ReporteRegistroInterface
{
    /**
     * @param  array{modulo?: string, fecha_desde?: string, fecha_hasta?: string}  $filtros
     */
    public function listarParaUsuario(Usuario $usuario, bool $esAdministrador, array $filtros = []): Collection;

    public function crear(array $atributos): ReporteRegistro;

    public function buscarPorId(int $id): ?ReporteRegistro;

    public function eliminar(ReporteRegistro $registro): bool;
}
