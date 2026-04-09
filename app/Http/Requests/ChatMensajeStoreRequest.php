<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ChatMensajeStoreRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'contenido' => 'required|string|min:1|max:8000',
        ];
    }

    public function messages(): array
    {
        return [
            'contenido.required' => 'Escribe un mensaje para enviar.',
            'contenido.max' => 'El mensaje no puede superar los 8000 caracteres.',
        ];
    }
}
