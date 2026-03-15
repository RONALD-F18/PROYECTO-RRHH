<?php

namespace App\Http\Controllers;

use App\Http\Requests\PrestacionSocialGestionarRequest;
use App\Services\PrestacionSocialService;

class PrestacionSocialController extends Controller
{
    protected $prestacionSocialService;

    public function __construct(PrestacionSocialService $prestacionSocialService)
    {
        $this->prestacionSocialService = $prestacionSocialService;
    }

    /**
     * Resumen: totales pendientes y contratos vigentes para liquidación.
     */
    public function index()
    {
        $totales = $this->prestacionSocialService->getTotalesPendientes();
        $contratosVigentes = $this->prestacionSocialService->getContratosVigentesParaLiquidacion();

        return response()->json([
            'message' => 'Resumen de prestaciones sociales',
            'data' => [
                'totales_pendientes' => $totales,
                'contratos_vigentes' => $contratosVigentes,
            ],
        ], 200);
    }

    /**
     * Totales globales de prestaciones pendientes (para dashboard).
     */
    public function totalesPendientes()
    {
        $totales = $this->prestacionSocialService->getTotalesPendientes();
        return response()->json([
            'message' => 'Totales de prestaciones pendientes',
            'data' => $totales,
        ], 200);
    }

    /**
     * Contrato con sus períodos de prestaciones (historial y pendientes).
     */
    public function showByContrato($cod_contrato)
    {
        $result = $this->prestacionSocialService->getContratoConPrestaciones($cod_contrato);
        if (!$result) {
            return response()->json(['message' => 'Contrato no encontrado'], 404);
        }
        return response()->json([
            'message' => 'Contrato y prestaciones',
            'data' => $result,
        ], 200);
    }

    /**
     * Calcular prestaciones para un contrato (desde último período o fecha ingreso hasta hoy).
     */
    public function calcular($cod_contrato)
    {
        try {
            $prestacion = $this->prestacionSocialService->calcularPrestaciones($cod_contrato);
            return response()->json([
                'message' => 'Prestaciones calculadas correctamente',
                'data' => $prestacion,
            ], 201);
        } catch (\InvalidArgumentException $e) {
            return response()->json(['message' => $e->getMessage()], 422);
        }
    }

    /**
     * Cambiar estado de un período: Pendiente → Pagado o Trasladado.
     */
    public function gestionar(PrestacionSocialGestionarRequest $request)
    {
        try {
            $id = $request->validated()['cod_prestacion_social_periodo'];
            $estado = $request->validated()['estado_pago'];
            $prestacion = $this->prestacionSocialService->actualizarEstado($id, $estado);
            if (!$prestacion) {
                return response()->json(['message' => 'Período no encontrado'], 404);
            }
            return response()->json([
                'message' => 'Estado actualizado correctamente',
                'data' => $prestacion,
            ], 200);
        } catch (\InvalidArgumentException $e) {
            return response()->json(['message' => $e->getMessage()], 422);
        }
    }

    /**
     * Eliminar un período solo si está en estado Pendiente.
     */
    public function destroy($cod_prestacion_social_periodo)
    {
        try {
            $deleted = $this->prestacionSocialService->eliminarPrestacion($cod_prestacion_social_periodo);
            if (!$deleted) {
                return response()->json(['message' => 'Período no encontrado'], 404);
            }
            return response()->json(['message' => 'Período eliminado correctamente'], 200);
        } catch (\InvalidArgumentException $e) {
            return response()->json(['message' => $e->getMessage()], 422);
        }
    }

    /**
     * Listar todos los períodos de prestaciones (con contrato, empleado, cargo).
     */
    public function listarTodos()
    {
        $prestaciones = $this->prestacionSocialService->getAllPrestacionesSociales();
        return response()->json([
            'message' => 'Lista de períodos de prestaciones',
            'data' => $prestaciones,
        ], 200);
    }
}
