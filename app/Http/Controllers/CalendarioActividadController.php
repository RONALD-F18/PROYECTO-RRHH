<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Services\CalendarioActividadesService;
use App\Http\Requests\CalendarioActividadesRequest;

class CalendarioActividadController extends Controller
{
    protected $calendarioActividadesService;
    
    public function __construct(CalendarioActividadesService $calendarioActividadesService)
    {
        $this->calendarioActividadesService = $calendarioActividadesService;
    }

    public function index()
    {
        $data = $this->calendarioActividadesService->getAllCalendarioActividades();

        return response()->json([
            'message' => 'Lista de calendario actividades obtenida exitosamente',
            'data' => $data
        ]);
    }

    public function show($id)
    {
        $data = $this->calendarioActividadesService->getCalendarioActividadById($id);

        if (!$data) {
            return response()->json([
                'message' => 'Calendario actividad no encontrado'
            ], 404);
        }

        return response()->json([
            'message' => 'Calendario actividad obtenido exitosamente',
            'data' => $data
        ]);
    }

    public function store(CalendarioActividadesRequest $request)
    {
        $data = $this->calendarioActividadesService->createCalendarioActividad($request->validated());

        return response()->json([
            'message' => 'Calendario actividad creado exitosamente',
            'data' => $data
        ], 201);
    }

    public function update(CalendarioActividadesRequest $request, $id)
    {
        $data = $this->calendarioActividadesService->updateCalendarioActividad($id, $request->validated());

        if (!$data) {
            return response()->json([
                'message' => 'Calendario actividad no encontrado'
            ], 404);
        }

        return response()->json([
            'message' => 'Calendario actividad actualizado exitosamente',
            'data' => $data
        ]);
    }
    
    public function destroy($id)
    {
        {
            $deleted = $this->calendarioActividadesService->deleteCalendarioActividad($id);

            if (!$deleted) {
                return response()->json([
                    'message' => 'Calendario actividad no encontrado'
                ], 404);
            }

            return response()->json([
                'message' => 'Calendario actividad eliminado exitosamente'
            ]);
        }
    }
}
