<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Requests\InasistenciaRequest;
use App\Services\InasistenciaService;

class InasistenciaController extends Controller
{
    protected $inasistenciaService;

    public function __construct(InasistenciaService $inasistenciaService)
    {
        $this->inasistenciaService = $inasistenciaService;
    }

    public function index()
    {
        $data = $this->inasistenciaService->getAllInasistencias();
        return response()->json([
            'message' => 'Lista de Inasistencias',
            'data' => $data
        ], 200);
    }

    public function show($id)
    {
        $data = $this->inasistenciaService->getInasistenciaById($id);
        if (!$data) {
            return response()->json([
                'message' => 'Inasistencia no encontrada'
            ], 404);
        }
        return response()->json([
            'message' => 'Inasistencia encontrada',
            'data' => $data
        ], 200);
    }

    public function store(InasistenciaRequest $request)
    {
        $data = $this->inasistenciaService->createInasistencia($request->validated());
        return response()->json([
            'message' => 'Inasistencia creada exitosamente',
            'data' => $data
        ], 201);
    }
}
