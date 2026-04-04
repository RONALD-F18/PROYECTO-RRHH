<?php

namespace App\Http\Requests;

use App\Models\Usuario;
use Illuminate\Foundation\Http\FormRequest;

class UsuarioRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Con enlace implícito de ruta, {usuario} puede ser el modelo; las reglas unique
     * necesitan el código numérico (cod_usuario), no el objeto.
     */
    protected function codigoUsuarioDesdeRuta(): string
    {
        $parametro = $this->route('usuario');
        if ($parametro instanceof Usuario) {
            return (string) $parametro->getRouteKey();
        }

        return (string) $parametro;
    }

    /** En pruebas automatizadas se evita validación DNS (entornos sin red fiable). */
    protected function reglasEmail(): string
    {
        return app()->environment('testing') ? 'rfc' : 'rfc,dns';
    }

    protected function prepareForValidation(): void
    {
        if ($this->isMethod('put') || $this->isMethod('patch')) {
            if ($this->has('contrasena_usuario') && $this->input('contrasena_usuario') === '') {
                $this->request->remove('contrasena_usuario');
            }
        }
    }

    public function rules(): array
    {
        $isUpdate = $this->isMethod('put') || $this->isMethod('patch');
        $codUsuario = $isUpdate ? $this->codigoUsuarioDesdeRuta() : '';
        $emailReglas = $this->reglasEmail();

        return [

            // nombre_usuario
            'nombre_usuario' => $isUpdate
                ? 'sometimes|bail|string|max:255|unique:usuarios,nombre_usuario,' . $codUsuario . ',cod_usuario'
                : 'required|bail|string|max:255|unique:usuarios,nombre_usuario',

            // email_usuario
            'email_usuario' => $isUpdate
                ? 'sometimes|bail|string|email:' . $emailReglas . '|max:255|unique:usuarios,email_usuario,' . $codUsuario . ',cod_usuario'
                : 'required|bail|string|email:' . $emailReglas . '|max:255|unique:usuarios,email_usuario',

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