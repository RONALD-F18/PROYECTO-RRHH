<?php

namespace App\Services;

use App\Models\Certificacion;
use App\Models\Contrato;
use App\Repositories\Interfaces\CertificacionInterface;
use Barryvdh\DomPDF\Facade\Pdf;

class CertificacionService
{
    protected $certificacionRepository;

    public function __construct(CertificacionInterface $certificacionRepository)
    {
        $this->certificacionRepository = $certificacionRepository;
    }

    public function listar()
    {
        return $this->certificacionRepository->getAll();
    }

    public function obtener($id)
    {
        return $this->certificacionRepository->getById($id);
    }

    public function crear(array $data)
    {
        if (!empty($data['incluye_salario']) &&
            empty($data['salario_certificado']) &&
            !empty($data['cod_contrato'])) {

            $contrato = Contrato::find($data['cod_contrato']);

            if ($contrato && isset($contrato->salario_basico)) {
                $data['salario_certificado'] = $contrato->salario_basico;
            }
        }

        return $this->certificacionRepository->create($data);
    }

    public function actualizar($id, array $data)
    {
        return $this->certificacionRepository->update($id, $data);
    }

    public function eliminar($id)
    {
        return $this->certificacionRepository->delete($id);
    }

    public function generarPdfLaboral(Certificacion $certificacion)
    {
        $certificacion->load(['empresa', 'empleado', 'contrato.cargo']);

        $pdf = Pdf::loadView('certificaciones.laboral', [
            'certificacion' => $certificacion,
            'empresa'       => $certificacion->empresa,
            'empleado'      => $certificacion->empleado,
            'contrato'      => $certificacion->contrato,
        ])->setPaper('letter', 'portrait');

        return $pdf->download('certificacion_laboral_' . $certificacion->cod_certificacion . '.pdf');
    }
}

