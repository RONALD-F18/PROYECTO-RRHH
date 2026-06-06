<?php

namespace App\Services;

use App\Models\Afiliacion;
use App\Models\CalendarioActividad;
use App\Models\Certificacion;
use App\Models\Contrato;
use App\Models\Empleado;
use App\Models\Inasistencia;
use App\Models\Incapacidad;
use Carbon\Carbon;

/**
 * Agregados para el panel (dashboard). No reemplaza los listados CRUD existentes.
 */
class DashboardService
{
    private const ETIQUETAS_MES = ['Ene', 'Feb', 'Mar', 'Abr', 'May', 'Jun', 'Jul', 'Ago', 'Sep', 'Oct', 'Nov', 'Dic'];

    public function getResumen(): array
    {
        $contratosVigentes = Contrato::query()
            ->where('estado_contrato', 'ACTIVO')
            ->count();

        $contratosOtros = Contrato::query()
            ->where('estado_contrato', '!=', 'ACTIVO')
            ->count();

        return [
            'empleados_activos' => Empleado::query()
                ->where('estado_emp', 'ACTIVO')
                ->count(),
            'contratos_vigentes' => $contratosVigentes,
            'contratos_otros' => $contratosOtros,
            'inasistencias_mes_actual' => $this->inasistenciasMesActual(),
            'incapacidades_total' => Incapacidad::query()->count(),
            'afiliaciones_total' => Afiliacion::query()->count(),
            'certificaciones_total' => Certificacion::query()->count(),
            'inasistencias_ultimos_6_meses' => $this->inasistenciasUltimosSeisMeses(),
            'contratos_pie' => [
                ['name' => 'Vigentes', 'value' => $contratosVigentes],
                ['name' => 'Finalizados u otros', 'value' => $contratosOtros],
            ],
            'actividades_recientes' => $this->actividadesRecientes(),
        ];
    }

    private function inasistenciasMesActual(): int
    {
        $hoy = Carbon::now();

        return Inasistencia::query()
            ->whereBetween('fecha_inasistencia', [
                $hoy->copy()->startOfMonth()->toDateString(),
                $hoy->copy()->endOfMonth()->toDateString(),
            ])
            ->count();
    }

    /**
     * @return list<array{clave: string, etiqueta: string, total: int}>
     */
    private function inasistenciasUltimosSeisMeses(): array
    {
        $series = [];

        for ($i = 5; $i >= 0; $i--) {
            $mes = Carbon::now()->subMonths($i);
            $series[] = [
                'clave' => $mes->format('Y-m'),
                'etiqueta' => self::ETIQUETAS_MES[(int) $mes->format('n') - 1],
                'total' => Inasistencia::query()
                    ->whereBetween('fecha_inasistencia', [
                        $mes->copy()->startOfMonth()->toDateString(),
                        $mes->copy()->endOfMonth()->toDateString(),
                    ])
                    ->count(),
            ];
        }

        return $series;
    }

    /**
     * @return list<array<string, mixed>>
     */
    private function actividadesRecientes(): array
    {
        return CalendarioActividad::query()
            ->select([
                'titulo',
                'tipo',
                'estado',
                'fecha_inicio',
                'fecha_creacion',
                'prioridad',
            ])
            ->orderByDesc('fecha_creacion')
            ->orderByDesc('cod_actividad')
            ->limit(6)
            ->get()
            ->map(function (CalendarioActividad $actividad) {
                return [
                    'titulo' => $actividad->titulo,
                    'tipo_actividad' => $actividad->tipo,
                    'estado' => $actividad->estado,
                    'fecha_inicio' => $this->formatearFecha($actividad->fecha_inicio),
                    'fecha_creacion' => $this->formatearFecha($actividad->fecha_creacion),
                    'prioridad' => $actividad->prioridad,
                ];
            })
            ->values()
            ->all();
    }

    private function formatearFecha(mixed $valor): ?string
    {
        if ($valor === null || $valor === '') {
            return null;
        }

        if ($valor instanceof Carbon) {
            return $valor->format('Y-m-d');
        }

        try {
            return Carbon::parse($valor)->format('Y-m-d');
        } catch (\Throwable) {
            return is_string($valor) ? $valor : null;
        }
    }
}
