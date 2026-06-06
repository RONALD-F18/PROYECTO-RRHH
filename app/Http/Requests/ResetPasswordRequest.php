<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ResetPasswordRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'email_usuario' => 'required|email|max:255',
            'token' => 'required|string|min:32|max:128',
            'contrasena_usuario' => [
                'required',
                'string',
                'min:8',
                'max:64',
                'confirmed',
                'regex:/^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)(?=.*[^A-Za-z0-9]).+$/',
            ],
        ];
    }

    public function messages(): array
    {
        return [
            'email_usuario.required' => 'El correo es obligatorio.',
            'email_usuario.email' => 'El correo no tiene un formato válido.',
            'token.required' => 'El token es obligatorio.',
            'token.min' => 'El enlace de recuperación no es válido.',
            'contrasena_usuario.required' => 'La nueva contraseña es obligatoria.',
            'contrasena_usuario.min' => 'La contraseña debe tener al menos 8 caracteres.',
            'contrasena_usuario.max' => 'La contraseña no puede superar 64 caracteres.',
            'contrasena_usuario.confirmed' => 'Las contraseñas no coinciden.',
            'contrasena_usuario.regex' => 'Debe incluir mayúscula, minúscula, número y un carácter especial.',
        ];
    }
}
