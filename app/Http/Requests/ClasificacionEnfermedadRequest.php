<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ClasificacionEnfermedadRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $isUpdate = $this->isMethod('put') || $this->isMethod('patch');

        return [
            'nombre_clasificacion' => $isUpdate ? 'bail|sometimes|required|string|max:150' : 'required|string|max:150',
            'codigo_cie10' => 'nullable|string|max:20',
            'descripcion' => 'nullable|string|max:200',
        ];
    }

    public function messages(): array
    {
        return [
            'nombre_clasificacion.required' => 'El nombre de la clasificación es obligatorio.',
        ];
    }
}
