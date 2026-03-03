<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule; 

class BancoRequest extends FormRequest
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
      $isMethodPut = $this->isMethod('put'); $this->isMethod('patch');
      return [
            'nombre_banco' => [
                'required',
                'string',
                'max:100',
                // Evita duplicados si es actualización
                $isMethodPut 
                    ? Rule::unique('bancos', 'nombre_banco')->ignore($bancoId)
                    : Rule::unique('bancos', 'nombre_banco')
            ],
            'descripcion_banco' => [
                'required',
                'string',
                'max:200',
            ],
        ];
    }

    /**
     * Mensajes personalizados para errores de validación.
     */
    public function messages(): array
    {
        return [
            'nombre_banco.required' => 'El nombre del banco es obligatorio.',
            'nombre_banco.string' => 'El nombre del banco debe ser texto.',
            'nombre_banco.max' => 'El nombre del banco no puede superar los 100 caracteres.',
            'nombre_banco.unique' => 'Este nombre de banco ya está registrado.',

            'descripcion_banco.required' => 'La descripción del banco es obligatoria.',
            'descripcion_banco.string' => 'La descripción del banco debe ser texto.',
            'descripcion_banco.max' => 'La descripción no puede superar los 200 caracteres.',
        ];
    }
}
