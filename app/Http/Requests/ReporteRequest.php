<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ReporteRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $isMethodPut = $this->isMethod('put') || $this->isMethod('patch');

        return [
            'modulo' => $isMethodPut
                ? 'bail|sometimes|required|string|in:empleados,contratos,prestaciones,incapacidades,inasistencias,afiliaciones,disciplinario'
                : 'bail|required|string|in:empleados,contratos,prestaciones,incapacidades,inasistencias,afiliaciones,disciplinario',

            'tipo' => $isMethodPut
                ? 'bail|sometimes|required|string|max:50'
                : 'bail|required|string|max:50',

            'params.cod_empleado' => $isMethodPut
                ? 'bail|sometimes|nullable|integer|exists:empleados,cod_empleado'
                : 'bail|nullable|integer|exists:empleados,cod_empleado',

            'params.cod_contrato' => $isMethodPut
                ? 'bail|sometimes|nullable|integer|exists:contrato,cod_contrato'
                : 'bail|nullable|integer|exists:contrato,cod_contrato',

            'params.tipo_certificacion' => $isMethodPut
                ? 'bail|sometimes|nullable|string|max:30'
                : 'bail|nullable|string|max:30',

            'params.descripcion' => $isMethodPut
                ? 'bail|sometimes|nullable|string|max:150'
                : 'bail|nullable|string|max:150',
        ];
    }

    public function messages(): array
    {
        return [
            'modulo.required' => 'El módulo del reporte es obligatorio.',
            'modulo.in'       => 'El módulo seleccionado no es válido.',

            'tipo.required' => 'El tipo de reporte es obligatorio.',
            'tipo.max'      => 'El tipo de reporte no debe superar los 50 caracteres.',

            'params.cod_empleado.integer' => 'El código de empleado debe ser numérico.',
            'params.cod_empleado.exists'  => 'El empleado seleccionado no existe.',

            'params.cod_contrato.integer' => 'El código de contrato debe ser numérico.',
            'params.cod_contrato.exists'  => 'El contrato seleccionado no existe.',

            'params.tipo_certificacion.max' => 'El tipo de certificación no debe superar los 30 caracteres.',

            'params.descripcion.max' => 'La descripción del reporte no debe superar los 150 caracteres.',
        ];
    }
}

