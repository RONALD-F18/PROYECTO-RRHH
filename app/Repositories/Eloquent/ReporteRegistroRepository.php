<?php

namespace App\Repositories\Eloquent;

use App\Models\ReporteRegistro;
use App\Models\Usuario;
use App\Repositories\Interfaces\ReporteRegistroInterface;
use Illuminate\Database\Eloquent\Collection;

class ReporteRegistroRepository implements ReporteRegistroInterface
{
    public function listarParaUsuario(Usuario $usuario, bool $esAdministrador, array $filtros = []): Collection
    {
        $q = ReporteRegistro::query()
            ->with(['usuario'])
            ->orderByDesc('created_at');

        if (! $esAdministrador) {
            $q->where('cod_usuario', $usuario->cod_usuario);
        }

        if (! empty($filtros['modulo'])) {
            $q->where('modulo', $filtros['modulo']);
        }

        if (! empty($filtros['fecha_desde'])) {
            $q->whereDate('created_at', '>=', $filtros['fecha_desde']);
        }

        if (! empty($filtros['fecha_hasta'])) {
            $q->whereDate('created_at', '<=', $filtros['fecha_hasta']);
        }

        return $q->get();
    }

    public function crear(array $atributos): ReporteRegistro
    {
        return ReporteRegistro::query()->create($atributos);
    }

    public function buscarPorId(int $id): ?ReporteRegistro
    {
        return ReporteRegistro::query()->with(['usuario'])->find($id);
    }

    public function eliminar(ReporteRegistro $registro): bool
    {
        return (bool) $registro->delete();
    }
}
