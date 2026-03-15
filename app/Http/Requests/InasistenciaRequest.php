<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class InasistenciaRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        $isMethodPut = $this->isMethod('put') || $this->isMethod('patch'); 

        return [
            'motivo_inasistencia' => $isMethodPut
            ? 'bail|sometimes|required|string|max:50'
            : 'required|string|max:50',
            'fecha_inasistencia' => $isMethodPut
            ? 'bail|sometimes|required|date'
            : 'required|date',
            'cod_empleado' => $isMethodPut
            ? 'bail|sometimes|nullable|exists:empleados,cod_empleado'
            : 'bail|nullable|exists:empleados,cod_empleado',
            'observaciones' => $isMethodPut
            ? 'bail|sometimes|nullable|string|max:80'
            : 'bail|nullable|string|max:80',
            'justificado' => $isMethodPut
            ? 'bail|sometimes|nullable|string|max:2'
            : 'bail|nullable|string|max:2',
        ];

    }   
    public function messages(): array
    {
        return [
            'motivo_inasistencia.required' => 'El motivo de inasistencia es obligatorio.',
            'motivo_inasistencia.string' => 'El motivo de inasistencia debe ser una cadena de texto.',
            'motivo_inasistencia.max' => 'El motivo de inasistencia no debe exceder los 50 caracteres.',

            'fecha_inasistencia.required' => 'La fecha de inasistencia es obligatoria.',
            'fecha_inasistencia.date' => 'La fecha de inasistencia debe ser una fecha válida.',

            'cod_empleado.exists' => 'El código de empleado proporcionado no existe.',

            'observaciones.string' => 'Las observaciones deben ser una cadena de texto.',
            'observaciones.max' => 'Las observaciones no deben exceder los 80 caracteres.',

            'justificado.string' => 'El campo justificado debe ser una cadena de texto.',
            'justificado.max' => 'El campo justificado no debe exceder los 2 caracteres.',
        ];
    }




}
