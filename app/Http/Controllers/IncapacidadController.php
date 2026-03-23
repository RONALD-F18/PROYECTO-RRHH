<?php

namespace App\Http\Controllers;

use App\Http\Requests\IncapacidadRequest;
use App\Services\IncapacidadService;

class IncapacidadController extends Controller
{
    protected $incapacidadService;

    public function __construct(IncapacidadService $incapacidadService)
    {
        $this->incapacidadService = $incapacidadService;
    }

    /**
     * Lista todas las incapacidades (con tipo, empleado, clasificación).
     */
    public function index()
    {
        $data = $this->incapacidadService->getAllIncapacidades();
        return response()->json([
            'message' => 'Lista de incapacidades',
            'data' => $data,
        ], 200);
    }

    /**
     * Resumen para dashboard: total, activas, origen común, laboral, total días, costo total.
     */
    public function resumen()
    {
        $data = $this->incapacidadService->getResumen();
        return response()->json([
            'message' => 'Resumen de incapacidades',
            'data' => $data,
        ], 200);
    }

    /**
     * Incapacidades de un empleado.
     */
    public function byEmpleado($cod_empleado)
    {
        $data = $this->incapacidadService->getByEmpleadoId($cod_empleado);
        return response()->json([
            'message' => 'Incapacidades del empleado',
            'data' => $data,
        ], 200);
    }

    /**
     * Una incapacidad por ID con distribución de pagos (normativa colombiana).
     */
    public function show($cod_incapacidad)
    {
        $incapacidad = $this->incapacidadService->getIncapacidadById($cod_incapacidad);
        if (!$incapacidad) {
            return response()->json(['message' => 'Incapacidad no encontrada'], 404);
        }
        $distribucion = $this->incapacidadService->calcularDistribucionPagos($incapacidad);
        return response()->json([
            'message' => 'Incapacidad encontrada',
            'data' => [
                'incapacidad' => $incapacidad,
                'distribucion_pagos' => $distribucion,
            ],
        ], 200);
    }

    /**
     * Crear incapacidad. entidad_responsable se calcula automáticamente según normativa.
     */
    public function store(IncapacidadRequest $request)
    {
        $incapacidad = $this->incapacidadService->createIncapacidad($request->validated());
        return response()->json([
            'message' => 'Incapacidad creada exitosamente',
            'data' => $incapacidad,
        ], 201);
    }

    /**
     * Actualizar incapacidad. entidad_responsable se recalcula.
     */
    public function update(IncapacidadRequest $request, $cod_incapacidad)
    {
        $incapacidad = $this->incapacidadService->updateIncapacidad($cod_incapacidad, $request->validated());
        if (!$incapacidad) {
            return response()->json(['message' => 'Incapacidad no encontrada'], 404);
        }
        return response()->json([
            'message' => 'Incapacidad actualizada correctamente',
            'data' => $incapacidad,
        ], 200);
    }

    /**
     * Eliminar incapacidad.
     */
    public function destroy($cod_incapacidad)
    {
        $deleted = $this->incapacidadService->deleteIncapacidad($cod_incapacidad);
        if (!$deleted) {
            return response()->json(['message' => 'Incapacidad no encontrada'], 404);
        }
        return response()->json(['message' => 'Incapacidad eliminada correctamente'], 200);
    }
}
