<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Requests\CesantiaRequest;
use App\Services\CesantiaService;

class CesantiaController extends Controller
{
    protected $cesantiaService;

    public function __construct(CesantiaService $cesantiaService)
    {
        $this->cesantiaService = $cesantiaService;
    }

    public function index()
    {
        $data = $this->cesantiaService->getAllCesantias();
        return response()->json([
            'message' => 'Lista de Cesantias',
            'data' => $data
        ], 200);
    }

    public function show($id)
    {
        $data = $this->cesantiaService->getCesantiaById($id);
        if (!$data) {
            return response()->json([
                'message' => 'Cesantia no encontrada'
            ], 404);
        }
        return response()->json([
            'message' => 'Cesantia encontrada',
            'data' => $data
        ], 200);
    }

    public function store(CesantiaRequest $request)
    {
        $data = $this->cesantiaService->createCesantia($request->validated());
        return response()->json([
            'message' => 'Cesantia creada exitosamente',
            'data' => $data
        ], 201);
    }


    public function update(CesantiaRequest $request, $id)
    {
        $data = $this->cesantiaService->updateCesantia($id, $request->validated());
        if (!$data) {
            return response()->json([
                'message' => 'Cesantia no encontrada'
            ], 404);
        }
        return response()->json([
            'message' => 'Cesantia actualizada exitosamente',
            'data' => $data
        ], 200);
    }

    public function destroy($id)
    {
        $deleted = $this->cesantiaService->deleteCesantia($id);
        if (!$deleted) {
            return response()->json([
                'message' => 'Cesantia no encontrada'
            ], 404);
        }
        return response()->json([
            'message' => 'Cesantia eliminada exitosamente'
        ], 200);
    }
}