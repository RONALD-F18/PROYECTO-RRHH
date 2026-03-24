<?php

namespace App\Http\Controllers;

use App\Services\ComunicacionDisciplinariaService;
use App\Http\Requests\ComunicacionDisciplinariaRequest;

class ComunicacionDisciplinariaController extends Controller
{
    protected ComunicacionDisciplinariaService $service;

    public function __construct(ComunicacionDisciplinariaService $service)
    {
        $this->service = $service;
    }

    public function index()
    {
        $data = $this->service->getAllComunicacionesDisciplinarias();

        return response()->json([
            'message' => 'Comunicaciones disciplinarias listadas exitosamente',
            'data' => $data,
        ], 200);
    }

    public function show($id)
    {
        $comunicacion = $this->service->getComunicacionDisciplinariaById($id);
        if (!$comunicacion) {
            return response()->json([
                'message' => 'Comunicación disciplinaria no encontrada',
                'data' => null,
            ], 404);
        }

        return response()->json([
            'message' => 'Comunicación disciplinaria encontrada',
            'data' => $comunicacion,
        ], 200);
    }

    public function store(ComunicacionDisciplinariaRequest $request)
    {
        $data = $request->validated();
        $data['cod_usuario'] = auth('api')->id();

        $comunicacion = $this->service->createComunicacionDisciplinaria($data);

        return response()->json([
            'message' => 'Comunicación disciplinaria creada exitosamente',
            'data' => $comunicacion,
        ], 201);
    }

    public function update(ComunicacionDisciplinariaRequest $request, $id)
    {
        $comunicacion = $this->service->updateComunicacionDisciplinaria($id, $request->validated());
        if (!$comunicacion) {
            return response()->json([
                'message' => 'Comunicación disciplinaria no encontrada',
                'data' => null,
            ], 404);
        }

        return response()->json([
            'message' => 'Comunicación disciplinaria actualizada exitosamente',
            'data' => $comunicacion,
        ], 200);
    }

    public function destroy($id)
    {
        $deleted = $this->service->deleteComunicacionDisciplinaria($id);
        if (!$deleted) {
            return response()->json([
                'message' => 'Comunicación disciplinaria no encontrada',
                'data' => null,
            ], 404);
        }

        return response()->json([
            'message' => 'Comunicación disciplinaria eliminada exitosamente',
            'data' => null,
        ], 200);
    }
}

