<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class TipoIncapacidadRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $isUpdate = $this->isMethod('put') || $this->isMethod('patch');

        return [
            'nombre_tipo' => $isUpdate ? 'bail|sometimes|required|string|max:80' : 'required|string|max:80',
            'descripcion' => 'nullable|string|max:200',
            'clave_normativa' => 'nullable|string|max:30|in:origen_comun,laboral,maternidad,paternidad',
        ];
    }

    public function messages(): array
    {
        return [
            'nombre_tipo.required' => 'El nombre del tipo de incapacidad es obligatorio.',
            'clave_normativa.in' => 'La clave normativa debe ser: origen_comun, laboral, maternidad o paternidad.',
        ];
    }
}
