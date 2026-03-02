<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UsuarioRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $isUpdate = $this->isMethod('put') || $this->isMethod('patch');

        return [

            // nombre_usuario
            'nombre_usuario' => $isUpdate
                ? 'sometimes|bail|string|max:255|unique:usuarios,nombre_usuario,' . $this->route('usuario') . ',cod_usuario'
                : 'required|bail|string|max:255|unique:usuarios,nombre_usuario',

            // email_usuario
            'email_usuario' => $isUpdate
                ? 'sometimes|bail|string|email:rfc,dns|max:255|unique:usuarios,email_usuario,' . $this->route('usuario') . ',cod_usuario'
                : 'required|bail|string|email:rfc,dns|max:255|unique:usuarios,email_usuario',

            // contrasena_usuario
            'contrasena_usuario' => $isUpdate
                ? 'sometimes|bail|string|min:8|max:64'
                : 'required|bail|string|min:8|max:64',

            // cod_rol
            'cod_rol' => $isUpdate
                ? 'sometimes|bail|exists:roles,cod_rol'
                : 'required|bail|exists:roles,cod_rol',

            // estado_usuario
            'estado_usuario' => $isUpdate
                ? 'sometimes|bail|boolean'
                : 'required|bail|boolean',
        ];
    }

    public function messages(): array
    {
        return [

            // nombre_usuario
            'nombre_usuario.required' => 'El nombre de usuario es obligatorio.',
            'nombre_usuario.string' => 'El nombre de usuario debe ser texto.',
            'nombre_usuario.max' => 'El nombre de usuario no puede superar los 255 caracteres.',
            'nombre_usuario.unique' => 'El nombre de usuario ya existe.',

            // email_usuario
            'email_usuario.required' => 'El correo electrónico es obligatorio.',
            'email_usuario.email' => 'El correo electrónico debe tener un formato válido.',
            'email_usuario.max' => 'El correo electrónico no puede superar los 255 caracteres.',
            'email_usuario.unique' => 'El correo electrónico ya existe.',

            // contrasena_usuario
            'contrasena_usuario.required' => 'La contraseña es obligatoria.',
            'contrasena_usuario.min' => 'La contraseña debe tener al menos 8 caracteres.',
            'contrasena_usuario.max' => 'La contraseña no puede superar los 64 caracteres.',

            // cod_rol
            'cod_rol.required' => 'El rol es obligatorio.',
            'cod_rol.exists' => 'El rol seleccionado no existe.',

            // estado_usuario
            'estado_usuario.required' => 'El estado es obligatorio.',
            'estado_usuario.boolean' => 'El estado debe ser verdadero o falso.',
        ];
    }
}