<?php

namespace App\Services;

use App\Repositories\Interfaces\EmpleadoInterface;
use App\Repositories\Interfaces\ContratoInterface;
use App\Repositories\Interfaces\PrestacionSocialInterface;
use App\Repositories\Interfaces\IncapacidadInterface;
use App\Repositories\Interfaces\InasistenciaInterface;
use App\Repositories\Interfaces\AfiliacionInterface;
use App\Repositories\Interfaces\ComunicacionDisciplinariaInterface;
use App\Repositories\Interfaces\ReporteInterface;
use Carbon\Carbon;

/**
 * Servicio central de reportes gerenciales de RRHH.
 * Arma estructuras agregadas por módulo y además persiste
 * un registro en la tabla reportes (historial).
 */
class ReporteService
{
    public function __construct(
        protected EmpleadoInterface $empleadoRepository,
        protected ContratoInterface $contratoRepository,
        protected PrestacionSocialInterface $prestacionSocialRepository,
        protected IncapacidadInterface $incapacidadRepository,
        protected InasistenciaInterface $inasistenciaRepository,
        protected AfiliacionInterface $afiliacionRepository,
        protected ComunicacionDisciplinariaInterface $comunicacionRepository,
        protected ReporteInterface $reporteRepository,
    ) {
    }

    /**
     * Punto de entrada genérico. Devuelve un arreglo listo
     * para ser consumido por la vista PDF.
     */
    public function generarReporte(string $modulo, string $tipo, array $params = [], ?int $codUsuario = null): array
    {
        $payload = match ($modulo) {
            'empleados'       => $this->reporteEmpleados($tipo, $params),
            'contratos'       => $this->reporteContratos($tipo, $params),
            'prestaciones'    => $this->reportePrestaciones($tipo, $params),
            'incapacidades'   => $this->reporteIncapacidades($tipo, $params),
            'inasistencias'   => $this->reporteInasistencias($tipo, $params),
            'afiliaciones'    => $this->reporteAfiliaciones($tipo, $params),
            'disciplinario'   => $this->reporteDisciplinario($tipo, $params),
            default           => throw new \InvalidArgumentException('Módulo de reporte no soportado.'),
        };

        $reporte = $this->reporteRepository->createReporte([
            'cod_usuario'   => $codUsuario,
            'fecha_emision' => now()->toDateString(),
            'descripcion'   => $params['descripcion'] ?? null,
            'modulo'        => $modulo,
            'tipo_reporte'  => $tipo,
            'estado'        => 'Generado',
        ]);

        $payload['codigo'] = 'RPT-' . str_pad((string) $reporte->cod_reporte, 6, '0', STR_PAD_LEFT);
        $payload['fecha']  = $reporte->fecha_emision;

        return $payload;
    }

    /* ─────────────────── Empleados ─────────────────── */

    protected function reporteEmpleados(string $tipo, array $params): array
    {
        // Por ahora solo se expone un tipo general.
        $empleados = $this->empleadoRepository->GetAllEmpleados();

        $total       = $empleados->count();
        $activos     = $empleados->where('estado_emp', 'ACTIVO')->count();
        $inactivos   = $empleados->where('estado_emp', 'INACTIVO')->count();
        $suspendidos = $empleados->where('estado_emp', 'SUSPENDIDO')->count();

        $porEstado = $empleados
            ->groupBy('estado_emp')
            ->map(fn ($group) => $group->count())
            ->sortDesc();

        $porProfesion = $empleados
            ->groupBy('profesion')
            ->map(fn ($group) => $group->count())
            ->sortDesc()
            ->take(10);

        return [
            'modulo'     => 'Empleados',
            'tipo'       => 'Resumen general de empleados',
            'codigo'     => $this->buildCodigo('EMP'),
            'fecha'      => Carbon::now(),
            'parametros' => [
                'Estados incluidos' => 'ACTIVO / INACTIVO / SUSPENDIDO',
            ],
            'titulo'     => 'Resumen General de Empleados',
            'subtitulo'  => 'Totales por estado y principales profesiones',
            'totales'    => [
                ['label' => 'Total empleados', 'valor' => $total, 'detalle' => 'registros'],
                ['label' => 'Activos', 'valor' => $activos, 'detalle' => 'empleados'],
                ['label' => 'Inactivos', 'valor' => $inactivos, 'detalle' => 'empleados'],
                ['label' => 'Suspendidos', 'valor' => $suspendidos, 'detalle' => 'empleados'],
            ],
            'secciones'  => [
                [
                    'titulo'   => 'Empleados por estado',
                    'columnas' => ['Estado', 'Cantidad', '% del total'],
                    'filas'    => $porEstado->map(function ($cantidad, $estado) use ($total) {
                        $porcentaje = $total > 0 ? round(($cantidad / $total) * 100, 1) : 0;
                        return [$estado ?: 'SIN ESTADO', $cantidad, $porcentaje . ' %'];
                    })->values()->all(),
                ],
                [
                    'titulo'   => 'Top 10 profesiones',
                    'columnas' => ['Profesión', 'Empleados'],
                    'filas'    => $porProfesion->map(function ($cantidad, $profesion) {
                        return [$profesion ?: 'SIN REGISTRO', $cantidad];
                    })->values()->all(),
                ],
            ],
        ];
    }

    /* ─────────────────── Contratos ─────────────────── */

    protected function reporteContratos(string $tipo, array $params): array
    {
        $contratos = $this->contratoRepository->GetAllContratos();

        $total       = $contratos->count();
        $vigentes    = $contratos->whereIn('estado_contrato', ['ACTIVO', 'Vigente'])->count();
        $terminados  = $contratos->whereIn('estado_contrato', ['TERMINADO', 'Terminado'])->count();
        $suspendidos = $contratos->where('estado_contrato', 'SUSPENDIDO')->count();

        $porEstado = $contratos
            ->groupBy('estado_contrato')
            ->map(fn ($group) => $group->count())
            ->sortDesc();

        $porTipo = $contratos
            ->groupBy('tipo_contrato')
            ->map(fn ($group) => $group->count())
            ->sortDesc();

        return [
            'modulo'     => 'Contratos',
            'tipo'       => 'Resumen general de contratos',
            'codigo'     => $this->buildCodigo('CON'),
            'fecha'      => Carbon::now(),
            'parametros' => [],
            'titulo'     => 'Resumen General de Contratos',
            'subtitulo'  => 'Totales por estado y tipo de contrato',
            'totales'    => [
                ['label' => 'Total contratos', 'valor' => $total, 'detalle' => 'registros'],
                ['label' => 'Vigentes', 'valor' => $vigentes, 'detalle' => 'contratos'],
                ['label' => 'Terminados', 'valor' => $terminados, 'detalle' => 'contratos'],
                ['label' => 'Suspendidos', 'valor' => $suspendidos, 'detalle' => 'contratos'],
            ],
            'secciones'  => [
                [
                    'titulo'   => 'Contratos por estado',
                    'columnas' => ['Estado', 'Cantidad', '% del total'],
                    'filas'    => $porEstado->map(function ($cantidad, $estado) use ($total) {
                        $porcentaje = $total > 0 ? round(($cantidad / $total) * 100, 1) : 0;
                        return [$estado ?: 'SIN ESTADO', $cantidad, $porcentaje . ' %'];
                    })->values()->all(),
                ],
                [
                    'titulo'   => 'Contratos por tipo',
                    'columnas' => ['Tipo de contrato', 'Cantidad'],
                    'filas'    => $porTipo->map(function ($cantidad, $tipo) {
                        return [$tipo ?: 'SIN TIPO', $cantidad];
                    })->values()->all(),
                ],
            ],
        ];
    }

    /* ─────────────────── Prestaciones sociales ─────────────────── */

    protected function reportePrestaciones(string $tipo, array $params): array
    {
        $periodos = $this->prestacionSocialRepository->getAllPrestacionesSociales();

        $totalPeriodos = $periodos->count();
        $pendientes    = $periodos->where('estado_pago', 'Pendiente');
        $pagadas       = $periodos->where('estado_pago', 'Pagado');

        $suma = fn ($col, $collection) => (float) $collection->sum($col);

        $totalCesantiasPend   = $suma('cesantias_valor', $pendientes);
        $totalCesantiasPag    = $suma('cesantias_valor', $pagadas);
        $totalInteresesPend   = $suma('intereses_cesantias_valor', $pendientes);
        $totalPrimaPend       = $suma('prima_valor', $pendientes);
        $totalVacacionesPend  = $suma('vacaciones_valor', $pendientes);

        return [
            'modulo'     => 'Prestaciones sociales',
            'tipo'       => 'Resumen general de prestaciones',
            'codigo'     => $this->buildCodigo('PRE'),
            'fecha'      => Carbon::now(),
            'parametros' => [],
            'titulo'     => 'Resumen General de Prestaciones Sociales',
            'subtitulo'  => 'Cesantías, intereses, prima y vacaciones',
            'totales'    => [
                ['label' => 'Períodos calculados', 'valor' => $totalPeriodos, 'detalle' => 'registros'],
                ['label' => 'Períodos pendientes', 'valor' => $pendientes->count(), 'detalle' => 'por pagar / trasladar'],
                ['label' => 'Períodos pagados', 'valor' => $pagadas->count(), 'detalle' => 'registros'],
            ],
            'secciones'  => [
                [
                    'titulo'   => 'Totales pendientes por concepto',
                    'columnas' => ['Concepto', 'Total pendiente (COP)'],
                    'filas'    => [
                        ['Cesantías', number_format($totalCesantiasPend, 2, ',', '.')],
                        ['Intereses cesantías', number_format($totalInteresesPend, 2, ',', '.')],
                        ['Prima', number_format($totalPrimaPend, 2, ',', '.')],
                        ['Vacaciones', number_format($totalVacacionesPend, 2, ',', '.')],
                    ],
                ],
                [
                    'titulo'   => 'Totales pagados por concepto',
                    'columnas' => ['Concepto', 'Total pagado (COP)'],
                    'filas'    => [
                        ['Cesantías', number_format($totalCesantiasPag, 2, ',', '.')],
                    ],
                ],
            ],
        ];
    }

    /* ─────────────────── Incapacidades ─────────────────── */

    protected function reporteIncapacidades(string $tipo, array $params): array
    {
        $todas = $this->incapacidadRepository->getAllIncapacidades();
        $total = $todas->count();

        $porTipo = $todas->groupBy(function ($inc) {
            $tipo = $inc->tipoIncapacidad;
            return $tipo ? strtolower($tipo->clave_normativa ?? 'sin_tipo') : 'sin_tipo';
        })->map(fn ($g) => $g->count());

        $porEntidad = $todas->groupBy('entidad_responsable')->map(fn ($g) => $g->count());

        return [
            'modulo'     => 'Incapacidades',
            'tipo'       => 'Resumen general de incapacidades',
            'codigo'     => $this->buildCodigo('INC'),
            'fecha'      => Carbon::now(),
            'parametros' => [],
            'titulo'     => 'Resumen General de Incapacidades',
            'subtitulo'  => 'Distribución por tipo normativo y entidad responsable',
            'totales'    => [
                ['label' => 'Total incapacidades', 'valor' => $total, 'detalle' => 'registros'],
            ],
            'secciones'  => [
                [
                    'titulo'   => 'Incapacidades por tipo normativo',
                    'columnas' => ['Tipo / Origen normativo', 'Cantidad', '% del total'],
                    'filas'    => $porTipo->map(function ($cantidad, $clave) use ($total) {
                        $porcentaje = $total > 0 ? round(($cantidad / $total) * 100, 1) : 0;
                        return [strtoupper($clave ?: 'SIN TIPO'), $cantidad, $porcentaje . ' %'];
                    })->values()->all(),
                ],
                [
                    'titulo'   => 'Incapacidades por entidad responsable',
                    'columnas' => ['Entidad responsable', 'Cantidad'],
                    'filas'    => $porEntidad->map(function ($cantidad, $entidad) {
                        return [$entidad ?: 'SIN REGISTRO', $cantidad];
                    })->values()->all(),
                ],
            ],
        ];
    }

    /* ─────────────────── Inasistencias ─────────────────── */

    protected function reporteInasistencias(string $tipo, array $params): array
    {
        $inasistencias = $this->inasistenciaRepository->getAllInasistencias();
        $total         = $inasistencias->count();

        $justificadas   = $inasistencias->where('justificado', true)->count();
        $noJustificadas = $inasistencias->where('justificado', false)->count();

        $porMes = $inasistencias->groupBy(function ($i) {
            return Carbon::parse($i->fecha_inasistencia)->format('Y-m');
        })->map(fn ($g) => $g->count())->sortKeys();

        return [
            'modulo'     => 'Inasistencias',
            'tipo'       => 'Resumen general de inasistencias',
            'codigo'     => $this->buildCodigo('INA'),
            'fecha'      => Carbon::now(),
            'parametros' => [],
            'titulo'     => 'Resumen General de Inasistencias',
            'subtitulo'  => 'Evolución mensual y justificación',
            'totales'    => [
                ['label' => 'Total inasistencias', 'valor' => $total, 'detalle' => 'registros'],
                ['label' => 'Justificadas', 'valor' => $justificadas, 'detalle' => 'casos'],
                ['label' => 'No justificadas', 'valor' => $noJustificadas, 'detalle' => 'casos'],
            ],
            'secciones'  => [
                [
                    'titulo'   => 'Inasistencias por mes',
                    'columnas' => ['Mes', 'Cantidad'],
                    'filas'    => $porMes->map(function ($cantidad, $mes) {
                        $label = Carbon::createFromFormat('Y-m', $mes)->translatedFormat('M Y');
                        return [$label, $cantidad];
                    })->values()->all(),
                ],
            ],
        ];
    }

    /* ─────────────────── Afiliaciones ─────────────────── */

    protected function reporteAfiliaciones(string $tipo, array $params): array
    {
        $afiliaciones = $this->afiliacionRepository->getAllAfiliaciones();
        $total        = $afiliaciones->count();

        $sinEps        = $afiliaciones->whereNull('cod_eps')->count();
        $sinArl        = $afiliaciones->whereNull('cod_arl')->count();
        $sinPensiones  = $afiliaciones->whereNull('cod_fondo_pensiones')->count();
        $sinCesantias  = $afiliaciones->whereNull('cod_fondo_cesantias')->count();
        $sinCaja       = $afiliaciones->whereNull('cod_caja_compensacion')->count();

        $porEps = $afiliaciones->groupBy('cod_eps')->map(fn ($g) => $g->count())->sortDesc()->take(10);
        $porArl = $afiliaciones->groupBy('cod_arl')->map(fn ($g) => $g->count())->sortDesc()->take(10);

        return [
            'modulo'     => 'Afiliaciones',
            'tipo'       => 'Resumen general de afiliaciones',
            'codigo'     => $this->buildCodigo('AFI'),
            'fecha'      => Carbon::now(),
            'parametros' => [],
            'titulo'     => 'Resumen General de Afiliaciones y Seguridad Social',
            'subtitulo'  => 'Cobertura EPS, ARL, pensiones, cesantías y caja',
            'totales'    => [
                ['label' => 'Total registros de afiliación', 'valor' => $total, 'detalle' => 'empleados'],
                ['label' => 'Sin EPS', 'valor' => $sinEps, 'detalle' => 'empleados'],
                ['label' => 'Sin ARL', 'valor' => $sinArl, 'detalle' => 'empleados'],
                ['label' => 'Sin pensiones', 'valor' => $sinPensiones, 'detalle' => 'empleados'],
                ['label' => 'Sin cesantías', 'valor' => $sinCesantias, 'detalle' => 'empleados'],
                ['label' => 'Sin caja compensación', 'valor' => $sinCaja, 'detalle' => 'empleados'],
            ],
            'secciones'  => [
                [
                    'titulo'   => 'Empleados por EPS',
                    'columnas' => ['EPS (código)', 'Empleados'],
                    'filas'    => $porEps->map(function ($cantidad, $codEps) {
                        return [$codEps ?: 'SIN CÓDIGO', $cantidad];
                    })->values()->all(),
                ],
                [
                    'titulo'   => 'Empleados por ARL',
                    'columnas' => ['ARL (código)', 'Empleados'],
                    'filas'    => $porArl->map(function ($cantidad, $codArl) {
                        return [$codArl ?: 'SIN CÓDIGO', $cantidad];
                    })->values()->all(),
                ],
            ],
        ];
    }

    /* ─────────────────── Comunicaciones disciplinarias ─────────────────── */

    protected function reporteDisciplinario(string $tipo, array $params): array
    {
        $comunicaciones = $this->comunicacionRepository->getAllComunicacionesDisciplinarias();
        $total          = $comunicaciones->count();

        $porTipo = $comunicaciones->groupBy('tipo_comunicacion')->map(fn ($g) => $g->count());
        $porEstado = $comunicaciones->groupBy('estado_comunicacion')->map(fn ($g) => $g->count());

        $totalSuspensiones = $comunicaciones->sum('dias_suspension');

        return [
            'modulo'     => 'Comunicaciones disciplinarias',
            'tipo'       => 'Resumen general disciplinario',
            'codigo'     => $this->buildCodigo('DIS'),
            'fecha'      => Carbon::now(),
            'parametros' => [],
            'titulo'     => 'Resumen General de Comunicaciones Disciplinarias',
            'subtitulo'  => 'Totales por tipo de comunicación y estado',
            'totales'    => [
                ['label' => 'Total comunicaciones', 'valor' => $total, 'detalle' => 'registros'],
                ['label' => 'Días totales de suspensión', 'valor' => $totalSuspensiones, 'detalle' => 'días'],
            ],
            'secciones'  => [
                [
                    'titulo'   => 'Comunicaciones por tipo',
                    'columnas' => ['Tipo de comunicación', 'Cantidad'],
                    'filas'    => $porTipo->map(function ($cantidad, $tipo) {
                        return [$tipo ?: 'SIN TIPO', $cantidad];
                    })->values()->all(),
                ],
                [
                    'titulo'   => 'Comunicaciones por estado',
                    'columnas' => ['Estado', 'Cantidad'],
                    'filas'    => $porEstado->map(function ($cantidad, $estado) {
                        return [$estado ?: 'SIN ESTADO', $cantidad];
                    })->values()->all(),
                ],
            ],
        ];
    }

    /* ─────────────────── Utilidades ─────────────────── */

    protected function buildCodigo(string $prefijo): string
    {
        $numero = now()->format('YmdHis');
        return $prefijo . '-' . $numero;
    }
}

