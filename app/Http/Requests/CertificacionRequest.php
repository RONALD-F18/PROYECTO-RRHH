<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class CertificacionRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $certificacionId = $this->route('certificacion');

        return [
            'id_empresa' => [
                'bail',
                $this->isMethod('post') ? 'required' : 'sometimes',
                'integer',
                'exists:empresas,id_empresa',
            ],
            'cod_empleado' => [
                'bail',
                $this->isMethod('post') ? 'required' : 'sometimes',
                'integer',
                'exists:empleados,cod_empleado',
            ],
            'cod_contrato' => [
                'bail',
                'nullable',
                'integer',
                'exists:contrato,cod_contrato',
            ],
            'tipo_certificacion' => [
                'bail',
                $this->isMethod('post') ? 'required' : 'sometimes',
                'string',
                'max:30',
            ],
            'incluye_salario' => [
                'bail',
                $this->isMethod('post') ? 'required' : 'sometimes',
                'boolean',
            ],
            'salario_certificado' => [
                'bail',
                'nullable',
                'numeric',
                'min:0',
            ],
            'cod_eps' => [
                'bail',
                'nullable',
                'integer',
                'exists:eps,cod_eps',
            ],
            'cod_arl' => [
                'bail',
                'nullable',
                'integer',
                'exists:arls,cod_arl',
            ],
            'cod_pension' => [
                'bail',
                'nullable',
                'integer',
                'exists:fondo_pensiones,cod_fondo_pensiones',
            ],
            'cod_caja' => [
                'bail',
                'nullable',
                'integer',
                'exists:caja_compensaciones,cod_caja_compensacion',
            ],
            'cod_cesantias' => [
                'bail',
                'nullable',
                'integer',
                'exists:fondo_cesantias,cod_fondo_cesantias',
            ],
            'fecha_emision' => [
                'bail',
                $this->isMethod('post') ? 'required' : 'sometimes',
                'date',
            ],
            'ciudad_emision' => [
                'bail',
                $this->isMethod('post') ? 'required' : 'sometimes',
                'string',
                'max:100',
            ],
            'descripcion' => [
                'bail',
                'nullable',
                'string',
                'max:150',
            ],
        ];
    }
}

