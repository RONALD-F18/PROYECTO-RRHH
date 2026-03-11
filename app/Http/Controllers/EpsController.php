<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Requests\EpsRequest;
use App\Services\EpsService;

class EpsController extends Controller
{
    protected $epsService;

    public function __construct(EpsService $epsService)
    {
        $this->epsService = $epsService;
    }

    public function index()
    {
        $data = $this->epsService->getAllEps();
        return response()->json([
            'message' => 'Lista de EPS',
            'data' => $data
        ], 200);
    }

    public function show($id)
    {
        $data = $this->epsService->getEpsById($id);
        if (!$data) {
            return response()->json([
                'message' => 'EPS no encontrada'
            ], 404);
        }
        return response()->json([
            'message' => 'EPS encontrada',
            'data' => $data
        ], 200);
    }

    public function store(EpsRequest $request)
    {
        $data = $this->epsService->createEps($request->validated());
        return response()->json([
            'message' => 'EPS creada exitosamente',
            'data' => $data
        ], 201);
    }


    public function update(EpsRequest $request, $id)
    {
        $data = $this->epsService->updateEps($id, $request->validated());
        if (!$data) {
            return response()->json([
                'message' => 'EPS no encontrada'
            ], 404);
        }
        return response()->json([
            'message' => 'EPS actualizada exitosamente',
            'data' => $data
        ], 200);
    }


    public function destroy($id)
    {
        $deleted = $this->epsService->deleteEps($id);
        if (!$deleted) {
            return response()->json([
                'message' => 'EPS no encontrada'
            ], 404);
        }
        return response()->json([
            'message' => 'EPS eliminada exitosamente'
        ], 200);
    }
}
