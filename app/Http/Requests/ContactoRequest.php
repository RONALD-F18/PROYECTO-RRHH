<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ContactoRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'nombre' => 'required|string|min:2|max:120',
            'email' => 'required|email:rfc|max:255',
            'asunto' => 'required|string|min:3|max:150',
            'mensaje' => 'required|string|min:10|max:2000',
        ];
    }

    public function messages(): array
    {
        return [
            'nombre.required' => 'El nombre es obligatorio.',
            'email.required' => 'El correo es obligatorio.',
            'email.email' => 'El correo no tiene un formato válido.',
            'asunto.required' => 'El asunto es obligatorio.',
            'mensaje.required' => 'El mensaje es obligatorio.',
            'mensaje.min' => 'El mensaje debe tener al menos 10 caracteres.',
        ];
    }
}
