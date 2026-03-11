<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Requests\RiesgoRequest;
use App\Services\RiesgoService; 

class RiesgoController extends Controller
{
    protected $riesgoService;

    public function __construct(RiesgoService $riesgoService)
    {
        $this->riesgoService = $riesgoService;
    }

    public function index()
    {
        $data = $this->riesgoService->getAllRiesgos();

        return response()->json([
            'message' => 'Riesgos listados exitosamente',
            'data' => $data
        ], 200);
    }

    public function show($id)
    {
        $data = $this->riesgoService->getRiesgoById($id);

        if (!$data) {
            return response()->json([
                'message' => 'Riesgo no encontrado'
            ], 404);
        }

        return response()->json([
            'message' => 'Riesgo obtenido exitosamente',
            'data' => $data
        ], 200);
    }

    public function store(RiesgoRequest $request)
    {
        $data = $this->riesgoService->createRiesgo($request->validated());

        return response()->json([
            'message' => 'Riesgo creado exitosamente',
            'data' => $data
        ], 201);
    }

    public function update(RiesgoRequest $request, $id)
    {
        $data = $this->riesgoService->updateRiesgo($id, $request->validated());

        if (!$data) {
            return response()->json([
                'message' => 'Riesgo no encontrado'
            ], 404);
        }

        return response()->json([
            'message' => 'Riesgo actualizado exitosamente',
            'data' => $data
        ], 200);
    }

    public function destroy($id)
    {
        $deleted = $this->riesgoService->deleteRiesgo($id);

        if (!$deleted) {
            return response()->json([
                'message' => 'Riesgo no encontrado'
            ], 404);
        }

        return response()->json([
            'message' => 'Riesgo eliminado exitosamente'
        ], 200);
}
}
