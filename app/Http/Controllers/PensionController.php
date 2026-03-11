<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Requests\PensionRequest;
use App\Services\PensionService;

class PensionController extends Controller
{
    protected $pensionService;

    public function __construct(PensionService $pensionService)
    {
        $this->pensionService = $pensionService;
    }

    public function index()
    {
        $data = $this->pensionService->getAllPensiones();

        return response()->json([
            'message' => 'Pensiones listados exitosamente',
            'data' => $data
        ], 200);
    }

    public function show($id)
    {
        $data = $this->pensionService->getPensionById($id);

        if (!$data) {
            return response()->json([
                'message' => 'Pensión no encontrada'
            ], 404);
        }

        return response()->json([
            'message' => 'Pensión obtenida exitosamente',
            'data' => $data
        ], 200);
    }

    public function store(PensionRequest $request)
    {
        $data = $this->pensionService->createPension($request->validated());

        return response()->json([
            'message' => 'Pensión creada exitosamente',
            'data' => $data
        ], 201);
    }

    public function update(PensionRequest $request, $id)
    {
        $data = $this->pensionService->updatePension($id, $request->validated());

        if (!$data) {
            return response()->json([
                'message' => 'Pensión no encontrada'
            ], 404);
        }

        return response()->json([
            'message' => 'Pensión actualizada exitosamente',
            'data' => $data
        ], 200);        
}

    public function destroy($id)
    {
        $deleted = $this->pensionService->deletePension($id);

        if (!$deleted) {
            return response()->json([
                'message' => 'Pensión no encontrada'
            ], 404);
        }
    }
}