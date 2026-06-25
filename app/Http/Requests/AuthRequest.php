<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class AuthRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    /** En testing se omite comprobación DNS (entornos sin red fiable). */
    protected function reglasEmail(): string
    {
        return app()->environment('testing') ? 'rfc' : 'rfc,dns';
    }

    public function rules(): array
    {
        $reglasEmail = $this->reglasEmail();

        return [
            'email_usuario' => [
                'bail',
                'required',
                'string',
                'email:'.$reglasEmail,
                'max:255',
                'regex:/^(?!.*\.\.)[A-Za-z0-9._%+\-]+@[A-Za-z0-9\-]+(\.[A-Za-z0-9\-]+)+$/',
            ],
            'contrasena_usuario' => [
                'bail',
                'required',
                'string',
                'min:8',
                'max:64',
            ],
        ];
    }

    protected function prepareForValidation(): void
    {
        if ($this->has('email_usuario') && is_string($this->email_usuario)) {
            $this->merge(['email_usuario' => mb_strtolower(trim($this->email_usuario))]);
        }
    }

    // Mensajes personalizados de error
    // Se envían al cliente cuando una validación falla
    public function messages(): array
    {
        return [

            // Mensajes para email
            'email_usuario.required' => 'El correo electrónico es obligatorio.',
            'email_usuario.email'    => 'El correo electrónico no es válido.',
            'email_usuario.regex'    => 'El correo electrónico contiene un formato inválido.',
            'email_usuario.max'      => 'El correo electrónico es demasiado largo.',

            // Mensajes para password
            // Nota: aquí se usan mensajes genéricos para no dar pistas de seguridad
            'contrasena_usuario.required' => 'La contraseña es obligatoria.',
            'contrasena_usuario.min'      => 'La contraseña es incorrecta.',
            'contrasena_usuario.max'      => 'La contraseña es incorrecta.',
        ];
    }
}
