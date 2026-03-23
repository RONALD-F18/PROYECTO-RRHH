<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ActividadCalendarioRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $isMethodPut = $this->isMethod('put') || $this->isMethod('patch');

        return [
            'titulo' => $isMethodPut
                ? 'bail|sometimes|required|string|max:50'
                : 'bail|required|string|max:50',

            'tipo' => $isMethodPut
                ? 'bail|sometimes|required|string|max:20'
                : 'bail|required|string|max:20',

            'fecha_inicio' => $isMethodPut
                ? 'bail|sometimes|required|date'
                : 'bail|required|date',

            'fecha_fin' => $isMethodPut
                ? 'bail|sometimes|nullable|date|after_or_equal:fecha_inicio'
                : 'bail|nullable|date|after_or_equal:fecha_inicio',

            'estado' => $isMethodPut
                ? 'bail|sometimes|required|string|max:20'
                : 'bail|required|string|max:20',

            'descripcion' => $isMethodPut
                ? 'bail|sometimes|nullable|string'
                : 'bail|nullable|string',

            'prioridad' => $isMethodPut
                ? 'bail|sometimes|required|string|max:20'
                : 'bail|required|string|max:20',

            'color' => $isMethodPut
                ? 'bail|sometimes|nullable|string|max:10'
                : 'bail|nullable|string|max:10',

            'cod_usuario' => $isMethodPut
                ? 'bail|sometimes|required|integer|exists:usuarios,cod_usuario'
                : 'bail|required|integer|exists:usuarios,cod_usuario',

            'fecha_creacion' => $isMethodPut
                ? 'bail|sometimes|nullable|date'
                : 'bail|nullable|date',

            'fecha_recordatorio' => $isMethodPut
                ? 'bail|sometimes|nullable|date|after_or_equal:fecha_inicio'
                : 'bail|nullable|date|after_or_equal:fecha_inicio',
        ];
    }
}

