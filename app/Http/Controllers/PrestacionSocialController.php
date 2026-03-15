<?php

namespace App\Http\Controllers;

use App\Http\Requests\PrestacionSocialGestionarRequest;
use App\Services\PrestacionSocialService;

/**
 * Controlador del módulo de Prestaciones Sociales (cesantías, intereses, prima, vacaciones).
 * Todas las acciones requieren autenticación (middleware auth.api).
 */
class PrestacionSocialController extends Controller
{
    protected $prestacionSocialService;

    public function __construct(PrestacionSocialService $prestacionSocialService)
    {
        $this->prestacionSocialService = $prestacionSocialService;
    }

    /**
     * Pantalla principal del módulo: totales pendientes + lista de contratos vigentes.
     * Ruta: GET prestaciones-sociales
     */
    public function index()
    {
        // Suma de cesantías, intereses, prima y vacaciones con estado_pago = Pendiente
        $totales = $this->prestacionSocialService->getTotalesPendientes();
        // Contratos activos/vigentes para mostrar fila "Ver prestaciones" por cada uno
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
     * Solo los totales pendientes (útil para dashboard o widgets).
     * Ruta: GET prestaciones-sociales/totales
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
     * Detalle de un contrato con empleado/cargo y todos sus períodos de prestaciones.
     * Ruta: GET contratos/{cod_contrato}/prestaciones
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
     * Calcula y guarda un nuevo período de prestaciones para el contrato (desde último periodo_fin o fecha_ingreso hasta hoy).
     * Ruta: POST contratos/{cod_contrato}/calcular-prestaciones
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
     * Marca un período como Pagado o Trasladado (solo si está Pendiente).
     * Body: cod_prestacion_social_periodo, estado_pago (Pagado|Trasladado).
     * Ruta: POST prestaciones-sociales/gestionar
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
     * Elimina un período de prestaciones. Solo permitido si estado_pago = Pendiente.
     * Ruta: DELETE prestaciones-sociales/{cod_prestacion_social_periodo}
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
     * Listado global: todos los períodos de todos los contratos (cada cálculo guardado).
     * Ruta: GET prestaciones-sociales/listar
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
