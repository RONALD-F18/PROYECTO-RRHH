<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class CompensacionRequest extends FormRequest
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
            'nombre' => [
                'bail',
                'required',
                'string',
                'max:255',
            ],

            'nit' => $isMethodPut
                ? 'sometimes|string|max:50'
                : 'required|string|max:50',

            'direccion' => $isMethodPut
                ? 'sometimes|string|max:255'
                : 'required|string|max:255',

            'telefono' => $isMethodPut
                ? 'sometimes|string|max:20'
                : 'required|string|max:20',

            'email' => $isMethodPut
                ? 'sometimes|email|max:255'
                : 'required|email|max:255',
        ];
    }

    public function messages(): array
    {
        return [
            'nombre.required' => 'El nombre de la caja de compensación es obligatorio.',
            'nombre.string' => 'El nombre de la caja de compensación debe ser una cadena de texto.',
            'nombre.max' => 'El nombre de la caja de compensación no debe exceder los 255 caracteres.',

            'nit.required' => 'El NIT de la caja de compensación es obligatorio.',
            'nit.string' => 'El NIT de la caja de compensación debe ser una cadena de texto.',
            'nit.max' => 'El NIT de la caja de compensación no debe exceder los 50 caracteres.',

            'direccion.required' => 'La dirección de la caja de compensación es obligatoria.',
            'direccion.string' => 'La dirección de la caja de compensación debe ser una cadena de texto.',
            'direccion.max' => 'La dirección de la caja de compensación no debe exceder los 255 caracteres.',

            'telefono.required' => 'El teléfono de la caja de compensación es obligatorio.',
            'telefono.string' => 'El teléfono de la caja de compensación debe ser una cadena de texto.',
            'telefono.max' => 'El teléfono de la caja de compensación no debe exceder los 20 caracteres.',

            'email.required' => 'El correo electrónico de la caja de compensación es obligatorio.',
            'email.email' => 'El correo electrónico de la caja de compensación debe ser una dirección válida.',
            'email.max' => 'El correo electrónico de la caja de compensación no debe exceder los 255 caracteres.',
        ];
    }
}
