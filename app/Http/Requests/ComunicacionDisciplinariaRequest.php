<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ComunicacionDisciplinariaRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $isUpdate = $this->isMethod('put') || $this->isMethod('patch');

        return [
            'tipo_comunicacion' => $isUpdate
                ? 'bail|sometimes|required|string|max:50'
                : 'bail|required|string|max:50',
            'fecha_emision' => $isUpdate
                ? 'bail|sometimes|required|date'
                : 'bail|required|date',
            'fecha_inicio_suspension' => $isUpdate
                ? 'bail|sometimes|nullable|date'
                : 'bail|nullable|date',
            'fecha_fin_suspension' => $isUpdate
                ? 'bail|sometimes|nullable|date'
                : 'bail|nullable|date',
            'estado_comunicacion' => $isUpdate
                ? 'bail|sometimes|required|string|max:20'
                : 'bail|required|string|max:20',
            'motivo_comunicacion' => $isUpdate
                ? 'bail|sometimes|required|string|max:20'
                : 'bail|required|string|max:20',
            'descripcion' => $isUpdate
                ? 'bail|sometimes|nullable|string'
                : 'bail|nullable|string',
            'dias_suspension' => $isUpdate
                ? 'bail|sometimes|nullable|integer|min:0'
                : 'bail|nullable|integer|min:0',
            'cod_empleado' => 'bail|required|exists:empleados,cod_empleado',
            'cod_usuario' => 'bail|required|exists:usuarios,cod_usuario',
        ];
    }

    public function messages(): array
    {
        return [
            'tipo_comunicacion.required' => 'El tipo de comunicación es obligatorio.',
            'tipo_comunicacion.string' => 'El tipo de comunicación debe ser una cadena de texto.',
            'tipo_comunicacion.max' => 'El tipo de comunicación no puede exceder los 50 caracteres.',

            'fecha_emision.required' => 'La fecha de emisión es obligatoria.',
            'fecha_emision.date' => 'La fecha de emisión debe ser una fecha válida.',

            'fecha_inicio_suspension.date' => 'La fecha de inicio de suspensión debe ser una fecha válida.',
            'fecha_fin_suspension.date' => 'La fecha de fin de suspensión debe ser una fecha válida.',

            'estado_comunicacion.required' => 'El estado de la comunicación es obligatorio.',
            'estado_comunicacion.string' => 'El estado de la comunicación debe ser una cadena de texto.',
            'estado_comunicacion.max' => 'El estado de la comunicación no puede exceder los 20 caracteres.',

            'motivo_comunicacion.required' => 'El motivo de la comunicación es obligatorio.',
            'motivo_comunicacion.string' => 'El motivo de la comunicación debe ser una cadena de texto.',
            'motivo_comunicacion.max' => 'El motivo de la comunicación no puede exceder los 20 caracteres.',

            'descripcion.string' => 'La descripción debe ser una cadena de texto.',

            'dias_suspension.integer' => 'Los días de suspensión deben ser un número entero.',
            'dias_suspension.min' => 'Los días de suspensión no pueden ser negativos.',

            'cod_empleado.required' => 'El código del empleado es obligatorio.',
            'cod_empleado.exists' => 'El código del empleado no existe en la base de datos.',

            'cod_usuario.required' => 'El código del usuario es obligatorio.',
            'cod_usuario.exists' => 'El código del usuario no existe en la base de datos.',
        ];
    }
}

