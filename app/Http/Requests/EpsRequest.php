<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class EpsRequest extends FormRequest
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
        'nombre_eps' => $isMethodPut
            ? 'sometimes|required|string|max:50|unique:eps,nombre_eps,' . $this->route('eps') . ',cod_eps'
            : 'required|string|max:50|unique:eps,nombre_eps',
        'descripcion_eps' => $isMethodPut
            ? 'sometimes|required|string|max:100'
            : 'required|string|max:100',
    ];

    }


    public function messages(): array
    {
        return [
            'nombre_eps.required' => 'El nombre de la EPS es obligatorio.',
            'nombre_eps.string' => 'El nombre de la EPS debe ser una cadena de texto.',
            'nombre_eps.max' => 'El nombre de la EPS no debe exceder los 50 caracteres.',
            'nombre_eps.unique' => 'El nombre de la EPS ya está en uso.',

            'descripcion_eps.required' => 'La descripción de la EPS es obligatoria.',
            'descripcion_eps.string' => 'La descripción de la EPS debe ser una cadena de texto.',
            'descripcion_eps.max' => 'La descripción de la EPS no debe exceder los 100 caracteres.',
        ];
    }
}
