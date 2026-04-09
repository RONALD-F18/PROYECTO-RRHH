<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ChatConversacionStoreRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'titulo' => 'nullable|string|max:150',
        ];
    }

    public function messages(): array
    {
        return [
            'titulo.max' => 'El título no puede superar los 150 caracteres.',
        ];
    }
}
