<?php

namespace App\Http\Controllers;

use App\Http\Requests\ClasificacionEnfermedadRequest;
use App\Services\ClasificacionEnfermedadService;

class ClasificacionEnfermedadController extends Controller
{
    protected ClasificacionEnfermedadService $clasificacionEnfermedadService;

    public function __construct(ClasificacionEnfermedadService $clasificacionEnfermedadService)
    {
        $this->clasificacionEnfermedadService = $clasificacionEnfermedadService;
    }

    public function index()
    {
        $data = $this->clasificacionEnfermedadService->getAllClasificacionesEnfermedad();
        return response()->json([
            'message' => 'Clasificaciones de enfermedad',
            'data' => $data,
        ], 200);
    }

    public function show($cod_clasificacion_enfermedad)
    {
        $data = $this->clasificacionEnfermedadService->getClasificacionEnfermedadById($cod_clasificacion_enfermedad);
        if (!$data) {
            return response()->json(['message' => 'Clasificación de enfermedad no encontrada'], 404);
        }
        return response()->json([
            'message' => 'Clasificación de enfermedad encontrada',
            'data' => $data,
        ], 200);
    }

    public function store(ClasificacionEnfermedadRequest $request)
    {
        $data = $this->clasificacionEnfermedadService->createClasificacionEnfermedad($request->validated());
        return response()->json([
            'message' => 'Clasificación de enfermedad creada exitosamente',
            'data' => $data,
        ], 201);
    }

    public function update(ClasificacionEnfermedadRequest $request, $cod_clasificacion_enfermedad)
    {
        $data = $this->clasificacionEnfermedadService->updateClasificacionEnfermedad($cod_clasificacion_enfermedad, $request->validated());
        if (!$data) {
            return response()->json(['message' => 'Clasificación de enfermedad no encontrada'], 404);
        }
        return response()->json([
            'message' => 'Clasificación de enfermedad actualizada correctamente',
            'data' => $data,
        ], 200);
    }

    public function destroy($cod_clasificacion_enfermedad)
    {
        $deleted = $this->clasificacionEnfermedadService->deleteClasificacionEnfermedad($cod_clasificacion_enfermedad);
        if (!$deleted) {
            return response()->json(['message' => 'Clasificación de enfermedad no encontrada'], 404);
        }
        return response()->json(['message' => 'Clasificación de enfermedad eliminada correctamente'], 200);
    }
}
