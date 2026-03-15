<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class PensionRequest extends FormRequest
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
        $id = $isMethodPut ? collect($this->route()->parameters())->first() : null;

        return [
            'nombre_fondo_pension' => $isMethodPut
            ? 'sometimes|required|string|max:50|unique:fondo_pensiones,nombre_fondo_pension,' . $id . ',cod_fondo_pensiones'
            : 'required|string|max:50|unique:fondo_pensiones,nombre_fondo_pension',
            'descripcion_fondo_pension' => $isMethodPut
            ? 'sometimes|required|string|max:100'
            : 'required|string|max:100',
        ];
    }

    public function messages(): array
    {
        return [
            'nombre_fondo_pension.required' => 'El nombre del fondo de pensiones es obligatorio.',
            'nombre_fondo_pension.string' => 'El nombre del fondo de pensiones debe ser una cadena de texto.',
            'nombre_fondo_pension.max' => 'El nombre del fondo de pensiones no debe exceder los 50 caracteres.',
            'nombre_fondo_pension.unique' => 'El nombre del fondo de pensiones ya está en uso.',

            'descripcion_fondo_pension.required' => 'La descripción del fondo de pensiones es obligatoria.',
            'descripcion_fondo_pension.string' => 'La descripción del fondo de pensiones debe ser una cadena de texto.',
            'descripcion_fondo_pension.max' => 'La descripción del fondo de pensiones no debe exceder los 100 caracteres.',
        ];
    }
}
