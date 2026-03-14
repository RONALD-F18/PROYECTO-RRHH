<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Requests\CompensacionRequest;
use App\Services\CompensacionService;

class CompensacionController extends Controller
{
    protected $compensacionService;

    public function __construct(CompensacionService $compensacionService)
    {
        $this->compensacionService = $compensacionService;
    }

    public function index()
    {
        $data = $this->compensacionService->getAllCompensaciones();
        return response()->json([
            'message' => 'Lista de Compensaciones',
            'data' => $data
        ], 200);
    }

    public function show($id)
    {
        $data = $this->compensacionService->getCompensacionById($id);
        if (!$data) {
            return response()->json([
                'message' => 'Compensacion no encontrada'
            ], 404);
        }
        return response()->json([
            'message' => 'Compensacion encontrada',
            'data' => $data
        ], 200);
    }

    public function store(CompensacionRequest $request)
    {
        $data = $this->prepareCompensacionData($request->validated());
        $data = $this->compensacionService->createCompensacion($data);
        return response()->json([
            'message' => 'Compensacion creada exitosamente',
            'data' => $data
        ], 201);
    }

    public function update(CompensacionRequest $request, $id)
    {
        $data = $this->prepareCompensacionData($request->validated());
        $data = $this->compensacionService->updateCompensacion($id, $data);
        if (!$data) {
            return response()->json([
                'message' => 'Compensacion no encontrada'
            ], 404);
        }
        return response()->json([
            'message' => 'Compensacion actualizada exitosamente',
            'data' => $data
        ], 200);
    }

    public function destroy($id)
    {
        $deleted = $this->compensacionService->deleteCompensacion($id);
        if (!$deleted) {
            return response()->json([
                'message' => 'Compensacion no encontrada'
            ], 404);
        }
    }

    /**
     * El Request valida "nombre"; el modelo y la BD usan "nombre_caja_compensacion".
     */
    private function prepareCompensacionData(array $data): array
    {
        if (array_key_exists('nombre', $data)) {
            $data['nombre_caja_compensacion'] = $data['nombre'];
            unset($data['nombre']);
        }
        return $data;
    }
}
