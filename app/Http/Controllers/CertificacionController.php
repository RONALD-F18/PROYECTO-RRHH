<?php

namespace App\Http\Controllers;

use App\Http\Requests\CertificacionRequest;
use App\Models\Certificacion;
use App\Services\CertificacionService;

class CertificacionController extends Controller
{
    protected $certificacionService;

    public function __construct(CertificacionService $certificacionService)
    {
        $this->certificacionService = $certificacionService;
    }

    public function index()
    {
        $data = $this->certificacionService->listar();

        return response()->json([
            'message' => 'Listado de certificaciones obtenido exitosamente',
            'data'    => $data,
        ]);
    }

    public function show($certificacion)
    {
        $data = $this->certificacionService->obtener($certificacion);

        if (!$data) {
            return response()->json(['message' => 'Certificación no encontrada'], 404);
        }

        return response()->json([
            'message' => 'Certificación obtenida exitosamente',
            'data'    => $data,
        ]);
    }

    public function store(CertificacionRequest $request)
    {
        $data = $this->certificacionService->crear($request->validated());

        return response()->json([
            'message' => 'Certificación creada exitosamente',
            'data'    => $data,
        ], 201);
    }

    public function update(CertificacionRequest $request, $certificacion)
    {
        $data = $this->certificacionService->actualizar($certificacion, $request->validated());

        if (!$data) {
            return response()->json(['message' => 'Certificación no encontrada'], 404);
        }

        return response()->json([
            'message' => 'Certificación actualizada exitosamente',
            'data'    => $data,
        ]);
    }

    public function destroy($certificacion)
    {
        $deleted = $this->certificacionService->eliminar($certificacion);

        if (!$deleted) {
            return response()->json(['message' => 'Certificación no encontrada'], 404);
        }

        return response()->json(['message' => 'Certificación eliminada exitosamente']);
    }

    public function descargarPdfLaboral($certificacion)
    {
        $modelo = Certificacion::findOrFail($certificacion);

        return $this->certificacionService->generarPdfLaboral($modelo);
    }
}

