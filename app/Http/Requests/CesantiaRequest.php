<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class CesantiaRequest extends FormRequest
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
        'nombre_fondo_cesantia' => [
            'bail',
            'required',
            'string',
            'max:50',
        ],

        'descripcion_fondo_cesantia' => $isMethodPut
            ? 'sometimes|string|max:100'
            : 'required|string|max:100',
    ];  
    }

    public function messages(): array
    {
        return [
            'nombre_fondo_cesantia.required' => 'El nombre del fondo de cesantía es obligatorio.',
            'nombre_fondo_cesantia.string' => 'El nombre del fondo de cesantía debe ser una cadena de texto.',
            'nombre_fondo_cesantia.max' => 'El nombre del fondo de cesantía no debe exceder los 50 caracteres.',
            'nombre_fondo_cesantia.unique' => 'El nombre del fondo de cesantía ya está en uso.',

            'descripcion_fondo_cesantia.required' => 'La descripción del fondo de cesantía es obligatoria.',
            'descripcion_fondo_cesantia.string' => 'La descripción del fondo de cesantía debe ser una cadena de texto.',
            'descripcion_fondo_cesantia.max' => 'La descripción del fondo de cesantía no debe exceder los 100 caracteres.',
        ];
    }   
}
