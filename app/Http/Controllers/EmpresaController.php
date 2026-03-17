<?php

namespace App\Http\Controllers;

use App\Http\Requests\EmpresaRequest;
use App\Services\EmpresaService;

class EmpresaController extends Controller
{
    protected $empresaService;

    public function __construct(EmpresaService $empresaService)
    {
        $this->empresaService = $empresaService;
    }

    public function index()
    {
        $data = $this->empresaService->getAllEmpresas();

        return response()->json([
            'message' => 'Listado de empresas obtenido exitosamente',
            'data'    => $data,
        ], 200);
    }

    public function show($empresa)
    {
        $data = $this->empresaService->getEmpresaById($empresa);

        if (!$data) {
            return response()->json(['message' => 'Empresa no encontrada'], 404);
        }

        return response()->json([
            'message' => 'Empresa obtenida exitosamente',
            'data'    => $data,
        ], 200);
    }

    public function store(EmpresaRequest $request)
    {
        $data = $this->empresaService->createEmpresa($request->validated());

        return response()->json([
            'message' => 'Empresa creada exitosamente',
            'data'    => $data,
        ], 200);
    }

    public function update(EmpresaRequest $request, $empresa)
    {
        $data = $this->empresaService->updateEmpresa($empresa, $request->validated());

        if (!$data) {
            return response()->json(['message' => 'Empresa no encontrada'], 404);
        }

        return response()->json([
            'message' => 'Empresa actualizada exitosamente',
            'data'    => $data,
        ], 200);
    }

    public function destroy($empresa)
    {
        $deleted = $this->empresaService->deleteEmpresa($empresa);

        if (!$deleted) {
            return response()->json(['message' => 'Empresa no encontrada'], 404);
        }

        return response()->json(['message' => 'Empresa eliminada exitosamente']);
    }
}

