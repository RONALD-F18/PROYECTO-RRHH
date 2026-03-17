<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Services\ComunicacionService;
use App\Http\Requests\ComunicacionRequest;

class ComunicacionController extends Controller
{
    protected $comunicacionService;

    public function __construct(ComunicacionService $comunicacionService)
    {
        $this->comunicacionService = $comunicacionService;
    }

    public function index()
    {
        $data = $this->comunicacionService->getAllComunicaciones();

        return response()->json([
            'message' => 'Comunicaciones listadas exitosamente',
            'data' => $data
        ], 200);
    }

    public function show($id)
    {
        $comunicacion = $this->comunicacionService->getComunicacionById($id);
        if (!$comunicacion) {
            return response()->json(['message' => 'Comunicación no encontrada'], 404);
        }
        return response()->json($comunicacion);
    }

    public function store(ComunicacionRequest $request)
    {
        $data = $request->validated();
        $comunicacion = $this->comunicacionService->createComunicacion($data);
        return response()->json($comunicacion, 201);
    }

    public function update(ComunicacionRequest $request, $id)
    {
        $data = $request->validated();
        $comunicacion = $this->comunicacionService->updateComunicacion($id, $data);
        if (!$comunicacion) {
            return response()->json(['message' => 'Comunicación no encontrada'], 404);
        }
        return response()->json($comunicacion);
    }

    public function destroy($id)
    {
        $deleted = $this->comunicacionService->deleteComunicacion($id);
        if (!$deleted) {
            return response()->json(['message' => 'Comunicación no encontrada'], 404);
        }
        return response()->json(['message' => 'Comunicación eliminada exitosamente']);
}
}