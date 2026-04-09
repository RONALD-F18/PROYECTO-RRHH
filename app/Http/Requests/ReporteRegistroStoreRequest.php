<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ReporteRegistroStoreRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'modulo' => ['bail', 'required', 'string', 'in:empleados,contratos,prestaciones,incapacidades,inasistencias,afiliaciones,disciplinario'],
            'tipo' => ['bail', 'required', 'string', 'max:50'],
            'estado' => ['bail', 'required', 'string', 'max:100'],
            'descripcion' => ['nullable', 'string', 'max:150'],
        ];
    }

    public function messages(): array
    {
        return [
            'modulo.required' => 'El módulo del reporte es obligatorio.',
            'modulo.in' => 'El módulo seleccionado no es válido.',
            'tipo.required' => 'El tipo de reporte es obligatorio.',
            'tipo.max' => 'El tipo de reporte no debe superar los 50 caracteres.',
            'estado.required' => 'El estado del reporte es obligatorio.',
            'estado.max' => 'El estado no debe superar los 100 caracteres.',
            'descripcion.max' => 'La descripción no debe superar los 150 caracteres.',
        ];
    }
}
