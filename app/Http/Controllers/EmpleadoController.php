<?php

namespace App\Http\Controllers;

use App\Http\Concerns\ResolvesPagination;
use App\Http\Requests\EmpleadoRequest;
use App\Models\Contrato;
use App\Services\EmpleadoService;
use App\Support\EmpleadoPayload;
use Illuminate\Http\JsonResponse;

class EmpleadoController extends Controller
{
    use ResolvesPagination;
    protected $empleadoService;

    public function __construct(EmpleadoService $empleadoService)
    {
        $this->empleadoService = $empleadoService;
    }

    public function index()
    {
        $resultado = $this->empleadoService->PaginateEmpleados(
            $this->resolvePage(),
            $this->resolvePerPage()
        );

        return response()->json([
            'message' => 'Empleados listados exitosamente',
            'data' => $resultado['data'],
            'meta' => $resultado['meta'],
        ], 200);
    }

    public function show($id)
    {
        $Empleado = $this->empleadoService->getEmpleadoById($id);
        if (!$Empleado) {
            return response()->json(['message' => 'Empleado no encontrado'], 404);
        }
        return response()->json($Empleado);
    }

    public function store(EmpleadoRequest $request): JsonResponse
    {
        $usuario = $request->user();
        if (! $usuario?->cod_usuario) {
            return response()->json(['message' => 'No autenticado.'], 401);
        }

        $data = EmpleadoPayload::paraCrear(
            $request->validated(),
            (int) $usuario->cod_usuario
        );

        $empleado = $this->empleadoService->createEmpleado($data);

        return response()->json($empleado, 201);
    }

    public function update(EmpleadoRequest $request, $id): JsonResponse
    {
        $data = EmpleadoPayload::paraActualizar($request->validated());

        if (($data['estado_emp'] ?? null) === 'RETIRADO') {
            $tieneContratoActivo = Contrato::query()
                ->where('cod_empleado', $id)
                ->where('estado_contrato', 'ACTIVO')
                ->exists();
            if ($tieneContratoActivo) {
                return response()->json([
                    'message' => 'No se puede marcar como RETIRADO mientras tenga un contrato en estado ACTIVO. Finalice el contrato primero.',
                ], 422);
            }
        }

        $Empleado = $this->empleadoService->updateEmpleado($id, $data);
        if (!$Empleado) {
            return response()->json(['message' => 'Empleado no encontrado'], 404);
        }
        return response()->json($Empleado);
    }

    public function destroy($id): JsonResponse
    {
        $deleted = $this->empleadoService->deleteEmpleado($id);
        if (!$deleted) {
            return response()->json(['message' => 'Empleado no encontrado'], 404);
        }
        return response()->json(['message' => 'Empleado eliminado']);
    }
}
