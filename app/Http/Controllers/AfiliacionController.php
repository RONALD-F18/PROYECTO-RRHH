<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Requests\AfiliacionRequest;
use App\Services\AfiliacionService;


//RRHH
//5
class AfiliacionController extends Controller
{
    protected $afiliacionService;

    public function __construct(AfiliacionService $afiliacionService)
    {
        $this->afiliacionService = $afiliacionService;
    }
    
    public function index()
    {
        $data = $this->afiliacionService->getAllAfiliaciones();
        return response()->json([
            'message' => 'Lista de Afiliaciones',
            'data' => $data
        ], 200);
    }

    public function show($id)
    {
        $data = $this->afiliacionService->getAfiliacionById($id);
        if (!$data) {
            return response()->json([
                'message' => 'Afiliacion no encontrada'
            ], 404);
        }
        return response()->json([
            'message' => 'Afiliacion encontrada',
            'data' => $data
        ], 200);
    }

    public function store(AfiliacionRequest $request)
    {
        $data = $this->afiliacionService->createAfiliacion($request->validated());
        return response()->json([
            'message' => 'Afiliacion creada exitosamente',
            'data' => $data
        ], 201);    
    }

    public function update(AfiliacionRequest $request, $id)
    {
        $data = $this->afiliacionService->updateAfiliacion($id, $request->validated());
        if (!$data) {
            return response()->json([
                'message' => 'Afiliacion no encontrada'
            ], 404);
        }
        return response()->json([
            'message' => 'Afiliación actualizada exitosamente',
            'data' => $data
        ], 200);
    }

    public function destroy($id)
    {
        $deleted = $this->afiliacionService->deleteAfiliacion($id);
        if (!$deleted) {
            return response()->json([
                'message' => 'Afiliacion no encontrada'
            ], 404);
        }
        return response()->json([
            'message' => 'Afiliación eliminada exitosamente'
        ], 200);
    }
}