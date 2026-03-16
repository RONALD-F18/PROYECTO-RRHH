<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class CalendarioActividadesRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Validation rules
     */
    public function rules(): array
    {
        $ismethodPut = $this->isMethod('put') || $this->isMethod('patch');

        return [
            'titulo' => $ismethodPut
                ? 'bail|sometimes|required|string|max:50'
                : 'bail|required|string|max:50',

            'tipo' => $ismethodPut
                ? 'bail|sometimes|required|string|max:20'
                : 'bail|required|string|max:20',

            'fecha_inicio' => $ismethodPut
                ? 'bail|sometimes|required|date'
                : 'bail|required|date',

            'fecha_fin' => $ismethodPut
                ? 'bail|sometimes|nullable|date|after_or_equal:fecha_inicio'
                : 'bail|nullable|date|after_or_equal:fecha_inicio',

            'estado' => $ismethodPut
                ? 'bail|sometimes|required|string|max:20'
                : 'bail|required|string|max:20',

            'descripcion' => $ismethodPut
                ? 'bail|sometimes|nullable|string'
                : 'bail|nullable|string',

            'prioridad' => $ismethodPut
                ? 'bail|sometimes|required|string|max:20'
                : 'bail|required|string|max:20',

            'color' => $ismethodPut
                ? 'bail|sometimes|nullable|string|max:10'
                : 'bail|nullable|string|max:10',

            'cod_usuario' => $ismethodPut
                ? 'bail|sometimes|required|exists:users,id'
                : 'bail|required|exists:users,id',

            'fecha_creacion' => $ismethodPut
                ? 'bail|sometimes|nullable|date'
                : 'bail|nullable|date',

            'fecha_recordatorio' => $ismethodPut
                ? 'bail|sometimes|nullable|date|after_or_equal:fecha_inicio'
                : 'bail|nullable|date|after_or_equal:fecha_inicio',
        ];
    }

    /**
     * Custom validation messages
     */
    public function messages(): array
    {
        return [
            'titulo.required' => 'El título es obligatorio.',
            'titulo.string' => 'El título debe ser texto.',
            'titulo.max' => 'El título no puede superar los 50 caracteres.',

            'tipo.required' => 'El tipo es obligatorio.',
            'tipo.string' => 'El tipo debe ser texto.',
            'tipo.max' => 'El tipo no puede superar los 20 caracteres.',

            'fecha_inicio.required' => 'La fecha de inicio es obligatoria.',
            'fecha_inicio.date' => 'La fecha de inicio debe ser una fecha válida.',

            'fecha_fin.date' => 'La fecha de fin debe ser una fecha válida.',
            'fecha_fin.after_or_equal' => 'La fecha de fin debe ser igual o posterior a la fecha de inicio.',

            'estado.required' => 'El estado es obligatorio.',
            'estado.string' => 'El estado debe ser texto.',
            'estado.max' => 'El estado no puede superar los 20 caracteres.',

            'descripcion.string' => 'La descripción debe ser texto.',

            'prioridad.required' => 'La prioridad es obligatoria.',
            'prioridad.string' => 'La prioridad debe ser texto.',
            'prioridad.max' => 'La prioridad no puede superar los 20 caracteres.',

            'color.string' => 'El color debe ser texto.',
            'color.max' => 'El color no puede superar los 10 caracteres.',

            'cod_usuario.required' => 'El código de usuario es obligatorio.',
            'cod_usuario.exists' => 'El código de usuario no existe en la base de datos.',

            'fecha_creacion.date' => 'La fecha de creación debe ser una fecha válida.',

            'fecha_recordatorio.date' => 'La fecha de recordatorio debe ser una fecha válida.',
            'fecha_recordatorio.after_or_equal' => 'La fecha de recordatorio debe ser igual o posterior a la fecha de inicio.',
        ];
    }
}