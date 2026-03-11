<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class RiesgoRequest extends FormRequest
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
        'nombre_riesgo' => $isMethodPut
            ? 'sometimes|required|string|max:50|unique:riesgos,nombre_riesgo,' . $this->route('riesgo') . ',cod_riesgo'
            : 'required|string|max:50|unique:riesgos,nombre_riesgo',
        'descripcion_riesgo' => $isMethodPut
            ? 'sometimes|required|string|max:100'
            : 'required|string|max:100',    
        ];
    }

    public function messages(): array
    {
        return [
            'nombre_riesgo.required' => 'El nombre del riesgo es obligatorio.',
            'nombre_riesgo.string' => 'El nombre del riesgo debe ser una cadena de texto.',
            'nombre_riesgo.max' => 'El nombre del riesgo no debe exceder los 50 caracteres.',
            'nombre_riesgo.unique' => 'El nombre del riesgo ya está en uso.',

            'descripcion_riesgo.required' => 'La descripción del riesgo es obligatoria.',
            'descripcion_riesgo.string' => 'La descripción del riesgo debe ser una cadena de texto.',
            'descripcion_riesgo.max' => 'La descripción del riesgo no debe exceder los 100 caracteres.',
        ];
    }   
        
}
