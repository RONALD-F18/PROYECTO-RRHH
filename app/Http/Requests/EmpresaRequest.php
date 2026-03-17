<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class EmpresaRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $empresaId = $this->route('empresa');

        return [
            'nit' => [
                'bail',
                $this->isMethod('post') ? 'required' : 'sometimes',
                'string',
                'max:20',
                'unique:empresas,nit,' . $empresaId . ',id_empresa',
            ],
            'dv' => [
                'bail',
                $this->isMethod('post') ? 'required' : 'sometimes',
                'string',
                'max:2',
            ],
            'razon_social' => [
                'bail',
                $this->isMethod('post') ? 'required' : 'sometimes',
                'string',
                'max:150',
            ],
            'nombre_comercial' => [
                'bail',
                'nullable',
                'string',
                'max:150',
            ],
            'tipo_empresa' => [
                'bail',
                'nullable',
                'string',
                'max:20',
            ],
            'estado_empresa' => [
                'bail',
                'nullable',
                'string',
                'max:20',
            ],
            'fecha_constitucion' => [
                'bail',
                'nullable',
                'date',
            ],
            'direccion' => [
                'bail',
                'nullable',
                'string',
                'max:200',
            ],
            'ciudad' => [
                'bail',
                'nullable',
                'string',
                'max:100',
            ],
            'departamento' => [
                'bail',
                'nullable',
                'string',
                'max:100',
            ],
            'pais' => [
                'bail',
                'nullable',
                'string',
                'max:50',
            ],
            'telefono' => [
                'bail',
                'nullable',
                'string',
                'max:20',
            ],
            'correo' => [
                'bail',
                'nullable',
                'email',
                'max:100',
            ],
            'pagina_web' => [
                'bail',
                'nullable',
                'string',
                'max:100',
            ],
            'nombre_representante' => [
                'bail',
                'nullable',
                'string',
                'max:150',
            ],
            'documento_representante' => [
                'bail',
                'nullable',
                'string',
                'max:20',
            ],
        ];
    }
}

