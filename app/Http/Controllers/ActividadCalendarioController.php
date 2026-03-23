<?php

namespace App\Http\Controllers;

use App\Services\ActividadCalendarioService;
use App\Http\Requests\ActividadCalendarioRequest;

class ActividadCalendarioController extends Controller
{
    protected $actividadCalendarioService;
    
    public function __construct(ActividadCalendarioService $actividadCalendarioService)
    {
        $this->actividadCalendarioService = $actividadCalendarioService;
    }

    public function index()
    {
        $data = $this->actividadCalendarioService->getAllActividadesCalendario();

        return response()->json([
            'message' => 'Lista de actividades de calendario obtenida exitosamente',
            'data' => $data
        ]);
    }

    public function show($id)
    {
        $data = $this->actividadCalendarioService->getActividadCalendarioById($id);

        if (!$data) {
            return response()->json([
                'message' => 'Actividad de calendario no encontrada'
            ], 404);
        }

        return response()->json([
            'message' => 'Actividad de calendario obtenida exitosamente',
            'data' => $data
        ]);
    }

    public function store(ActividadCalendarioRequest $request)
    {
        $data = $this->actividadCalendarioService->createActividadCalendario($request->validated());

        return response()->json([
            'message' => 'Actividad de calendario creada exitosamente',
            'data' => $data
        ], 201);
    }

    public function update(ActividadCalendarioRequest $request, $id)
    {
        $data = $this->actividadCalendarioService->updateActividadCalendario($id, $request->validated());

        if (!$data) {
            return response()->json([
                'message' => 'Actividad de calendario no encontrada'
            ], 404);
        }

        return response()->json([
            'message' => 'Actividad de calendario actualizada exitosamente',
            'data' => $data
        ]);
    }
    
    public function destroy($id)
    {
        $deleted = $this->actividadCalendarioService->deleteActividadCalendario($id);

        if (!$deleted) {
            return response()->json([
                'message' => 'Actividad de calendario no encontrada'
            ], 404);
        }

        return response()->json([
            'message' => 'Actividad de calendario eliminada exitosamente'
        ]);
    }
}

