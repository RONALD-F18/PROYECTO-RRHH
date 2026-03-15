<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class BancoRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $bancoId = $this->route('banco');

        return [

            'nombre_banco' => [
                'bail',
                'required',
                'string',
                'max:100',
                'regex:/^[\pL0-9\s]+$/u',
                $this->isMethod('put') || $this->isMethod('patch')
                    ? Rule::unique('bancos', 'nombre_banco')->ignore($bancoId, 'cod_banco')
                    : Rule::unique('bancos', 'nombre_banco'),
            ],

            'descripcion_banco' => $this->isMethod('post')
                ? ['bail', 'required', 'string', 'max:200']
                : ['bail', 'sometimes', 'string', 'max:200'],
        ];
    }

    public function messages(): array
    {
        return [
            'nombre_banco.required' => 'El nombre del banco es obligatorio.',
            'nombre_banco.string' => 'El nombre del banco debe ser texto.',
            'nombre_banco.max' => 'El nombre del banco no puede superar los 100 caracteres.',
            'nombre_banco.unique' => 'Este nombre de banco ya está registrado.',
            'nombre_banco.regex' => 'El nombre del banco solo puede contener letras, números y espacios.',

            'descripcion_banco.required' => 'La descripción del banco es obligatoria.',
            'descripcion_banco.string' => 'La descripción del banco debe ser texto.',
            'descripcion_banco.max' => 'La descripción no puede superar los 200 caracteres.',
        ];
    }
}