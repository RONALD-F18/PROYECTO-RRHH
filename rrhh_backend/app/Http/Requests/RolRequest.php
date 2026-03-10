<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class RolRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, mixed>
     */
    public function rules()
    {

        $isMethodPut = $this->isMethod('put') || $this->isMethod('patch');

        return [

            'nombre_rol' => $isMethodPut
                ? 'sometimes|required|string|unique:roles,nombre_rol,' . $this->route('rol') . ',cod_rol'
                : 'required|string|unique:roles,nombre_rol',

            'estado_rol' => $isMethodPut
                ? 'sometimes|required|boolean'
                : 'required|boolean',

            'descripcion' => $isMethodPut
                ? 'sometimes|nullable|string'
                : 'nullable|string',
        ];
    }

    public function messages()
    {
        return [
            'nombre_rol.required' => 'El nombre del rol es obligatorio.',
            'nombre_rol.string' => 'El nombre del rol debe ser una cadena de texto.',
            'nombre_rol.unique' => 'El nombre del rol ya existe. Por favor, elige otro nombre.',

            'estado_rol.required' => 'El estado del rol es obligatorio.',
            'estado_rol.boolean' => 'El estado del rol debe ser verdadero o falso (1 o 0).',

            'descripcion.string' => 'La descripción del rol debe ser una cadena de texto.',
        ];
    }
}
