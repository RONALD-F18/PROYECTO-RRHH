<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ForgotPasswordRequest extends FormRequest
{
    public function authorize(): bool { return true; }

    public function rules(): array
    {
        return [
            'email_usuario' => 'required|email|max:255',
        ];
    }

    public function messages(): array
    {
        return [
            'email_usuario.required' => 'El correo es obligatorio.',
            'email_usuario.email'    => 'El correo no tiene un formato válido.',
        ];
    }
}