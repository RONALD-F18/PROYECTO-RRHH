<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ResetPasswordRequest extends FormRequest
{
    public function authorize(): bool { return true; }

    public function rules(): array
    {
        return [
            'email_usuario'      => 'required|email|max:255',
            'token'              => 'required|string',
            // 'confirmed' busca automáticamente el campo contrasena_usuario_confirmation
            'contrasena_usuario' => 'required|string|min:8|max:64|confirmed',
        ];
    }

    public function messages(): array
    {
        return [
            'email_usuario.required'       => 'El correo es obligatorio.',
            'token.required'               => 'El token es obligatorio.',
            'contrasena_usuario.required'  => 'La nueva contraseña es obligatoria.',
            'contrasena_usuario.min'       => 'Mínimo 8 caracteres.',
            'contrasena_usuario.confirmed' => 'Las contraseñas no coinciden.',
        ];
    }
}