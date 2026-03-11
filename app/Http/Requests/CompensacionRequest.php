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
            'nombre' => $isMethodPut
            ? 'sometimes|required|string|max:50|unique:caja_compensaciones,nombre_caja_compensacion,' . $this->route('caja_compensacion') . ',cod_caja_compensacion'
            : 'required|string|max:50|unique:caja_compensaciones,nombre_caja_compensacion',
            'descripcion_caja_compensacion' => $isMethodPut
            ? 'sometimes|required|string|max:100'
            : 'required|string|max:100',
        ];
    }

    public function messages(): array
    {
        return [
            'nombre_caja_compensacion.required' => 'El nombre de la caja de compensación es obligatorio.',
            'nombre_caja_compensacion.string' => 'El nombre de la caja de compensación debe ser una cadena de texto.',
            'nombre_caja_compensacion.max' => 'El nombre de la caja de compensación no debe exceder los 50 caracteres.',
            'nombre_caja_compensacion.unique' => 'El nombre de la caja de compensación ya está en uso.',
            'descripcion_caja_compensacion.required' => 'La descripción de la caja de compensación es obligatoria.',
            'descripcion_caja_compensacion.string' => 'La descripción de la caja de compensación debe ser una cadena de texto.',
            'descripcion_caja_compensacion.max' => 'La descripción de la caja de compensación no debe exceder los 100 caracteres.',
        ];
    }
}
