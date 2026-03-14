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
            'nomb_cargo' => $isMethodPut
                ? 'bail|sometimes|required|string|max:50'
                : 'bail|required|string|max:50',

            'descripcion' => $isMethodPut
                ? 'bail|sometimes|nullable|string|max:150'
                : 'bail|nullable|string|max:150',
        ];
    }

    public function messages(): array
    {
        return [
            'nomb_cargo.required' => 'El nombre del cargo es obligatorio.',
            'nomb_cargo.string' => 'El nombre del cargo debe ser texto.',
            'nomb_cargo.max' => 'El nombre del cargo no puede superar los 50 caracteres.',

            'descripcion.string' => 'La descripción del cargo debe ser texto.',
            'descripcion.max' => 'La descripción del cargo no puede superar los 150 caracteres.',
        ];
    }
}
