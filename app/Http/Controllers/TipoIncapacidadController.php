<?php

namespace App\Http\Controllers;

use App\Http\Requests\TipoIncapacidadRequest;
use App\Services\TipoIncapacidadService;

class TipoIncapacidadController extends Controller
{
    protected TipoIncapacidadService $tipoIncapacidadService;

    public function __construct(TipoIncapacidadService $tipoIncapacidadService)
    {
        $this->tipoIncapacidadService = $tipoIncapacidadService;
    }

    public function index()
    {
        $data = $this->tipoIncapacidadService->getAllTiposIncapacidad();
        return response()->json([
            'message' => 'Tipos de incapacidad',
            'data' => $data,
        ], 200);
    }

    public function show($cod_tipo_incapacidad)
    {
        $data = $this->tipoIncapacidadService->getTipoIncapacidadById($cod_tipo_incapacidad);
        if (!$data) {
            return response()->json(['message' => 'Tipo de incapacidad no encontrado'], 404);
        }
        return response()->json([
            'message' => 'Tipo de incapacidad encontrado',
            'data' => $data,
        ], 200);
    }

    public function store(TipoIncapacidadRequest $request)
    {
        $data = $this->tipoIncapacidadService->createTipoIncapacidad($request->validated());
        return response()->json([
            'message' => 'Tipo de incapacidad creado exitosamente',
            'data' => $data,
        ], 201);
    }

    public function update(TipoIncapacidadRequest $request, $cod_tipo_incapacidad)
    {
        $data = $this->tipoIncapacidadService->updateTipoIncapacidad($cod_tipo_incapacidad, $request->validated());
        if (!$data) {
            return response()->json(['message' => 'Tipo de incapacidad no encontrado'], 404);
        }
        return response()->json([
            'message' => 'Tipo de incapacidad actualizado correctamente',
            'data' => $data,
        ], 200);
    }

    public function destroy($cod_tipo_incapacidad)
    {
        $deleted = $this->tipoIncapacidadService->deleteTipoIncapacidad($cod_tipo_incapacidad);
        if (!$deleted) {
            return response()->json(['message' => 'Tipo de incapacidad no encontrado'], 404);
        }
        return response()->json(['message' => 'Tipo de incapacidad eliminado correctamente'], 200);
    }
}
