<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class CargoRequest extends FormRequest
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
            'nombreCargo' => $isMethodPut
                ? 'bail|sometimes|required|string|max:100'
                : 'bail|required|string|max:100',

            'descripcionCargo' => $isMethodPut
                ? 'bail|sometimes|nullable|string|max:255'
                : 'bail|nullable|string|max:255',
        ];
    }

    public function messages(): array
    {
        return [
            'nombreCargo.required' => 'El nombre del cargo es obligatorio.',
            'nombreCargo.string' => 'El nombre del cargo debe ser texto.',
            'nombreCargo.max' => 'El nombre del cargo no puede superar los 100 caracteres.',

            'descripcionCargo.string' => 'La descripción del cargo debe ser texto.',
            'descripcionCargo.max' => 'La descripción del cargo no puede superar los 255 caracteres.',
        ];
    }
}
