<?php

namespace App\Http\Controllers;

use App\Http\Requests\InasistenciaRequest;
use App\Services\InasistenciaService;
use Illuminate\Http\Request;

class InasistenciaController extends Controller
{
    protected $inasistenciaService;

    public function __construct(InasistenciaService $inasistenciaService)
    {
        $this->inasistenciaService = $inasistenciaService;
    }

    /**
     * Filtros opcionales: cod_empleado, mes, anio.
     * Sin filtros devuelve todas las inasistencias.
     */
    public function index(Request $request)
    {
        $codEmpleado = $request->query('cod_empleado');
        $mes = $request->query('mes');
        $anio = $request->query('anio');

        $data = ($codEmpleado !== null || $mes !== null || $anio !== null)
            ? $this->inasistenciaService->buscarInasistencias(
                $codEmpleado !== null ? (int) $codEmpleado : null,
                $mes !== null ? (int) $mes : null,
                $anio !== null ? (int) $anio : null,
            )
            : $this->inasistenciaService->getAllInasistencias();

        return response()->json([
            'message' => 'Lista de Inasistencias',
            'data' => $data,
        ], 200);
    }

    public function byEmpleado(int $cod_empleado)
    {
        $data = $this->inasistenciaService->buscarInasistencias($cod_empleado);

        return response()->json([
            'message' => 'Inasistencias del empleado',
            'data' => $data,
        ], 200);
    }

    public function destroyByEmpleado(int $cod_empleado)
    {
        $eliminadas = $this->inasistenciaService->deleteByEmpleadoId($cod_empleado);

        return response()->json([
            'message' => $eliminadas > 0
                ? "Se eliminaron {$eliminadas} inasistencia(s) del empleado."
                : 'No había inasistencias registradas para este empleado.',
            'data' => ['eliminadas' => $eliminadas],
        ], 200);
    }

    public function show($id)
    {
        $data = $this->inasistenciaService->getInasistenciaById($id);
        if (!$data) {
            return response()->json([
                'message' => 'Inasistencia no encontrada',
            ], 404);
        }
        return response()->json([
            'message' => 'Inasistencia encontrada',
            'data' => $data,
        ], 200);
    }

    public function store(InasistenciaRequest $request)
    {
        $data = $this->inasistenciaService->createInasistencia($request->validated());
        return response()->json([
            'message' => 'Inasistencia creada exitosamente',
            'data' => $data,
        ], 201);
    }

    public function update(InasistenciaRequest $request, $id)
    {
        $data = $this->inasistenciaService->updateInasistencia($id, $request->validated());
        if (!$data) {
            return response()->json([
                'message' => 'Inasistencia no encontrada',
            ], 404);
        }
        return response()->json([
            'message' => 'Inasistencia actualizada exitosamente',
            'data' => $data,
        ], 200);
    }

    public function destroy($id)
    {
        $deleted = $this->inasistenciaService->deleteInasistencia($id);
        if (!$deleted) {
            return response()->json([
                'message' => 'Inasistencia no encontrada',
            ], 404);
        }
        return response()->json([
            'message' => 'Inasistencia eliminada exitosamente',
        ], 200);
    }
}
