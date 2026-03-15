<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ArlRequest extends FormRequest
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
        'nombre_arl' => $isMethodPut
            ? 'sometimes|required|string|max:50|unique:arls,nombre_arl,' . $this->route('arl') . ',cod_arl'
            : 'required|string|max:50|unique:arls,nombre_arl',
        'descripcion_arl' => $isMethodPut
            ? 'sometimes|required|string|max:100'
            : 'required|string|max:100',
    ];
}


    public function messages(): array
    {
        return [
            'nombre_arl.required' => 'El nombre de la ARL es obligatorio.',
            'nombre_arl.string' => 'El nombre de la ARL debe ser una cadena de texto.',
            'nombre_arl.max' => 'El nombre de la ARL no debe exceder los 50 caracteres.',
            'nombre_arl.unique' => 'El nombre de la ARL ya está en uso.',
            'descripcion_arl.required' => 'La descripción de la ARL es obligatoria.',
            'descripcion_arl.string' => 'La descripción de la ARL debe ser una cadena de texto.',
            'descripcion_arl.max' => 'La descripción de la ARL no debe exceder los 100 caracteres.',
        ];
    }
}
