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
    public function rules(): array
    {
        return [

            // Validación del campo email
            'email' => [

                // bail: detiene la validación en el primer error encontrado
                // evita validar reglas innecesarias y mejora rendimiento/seguridad
                'bail',

                // El campo es obligatorio
                'required',

                // Debe ser texto
                'string',

                // Valida formato de correo usando RFC y DNS (más estricto)
                'email:rfc,dns',

                // Longitud máxima permitida
                'max:255',
            ],

            // Validación del campo password
            'password' => [

                // Detiene validación al primer fallo
                'bail',

                // Campo obligatorio
                'required',

                // Debe ser texto
                'string',

                // Longitud mínima de contraseña
                'min:8',

                // Longitud máxima permitida
                'max:64',
            ],
        ];
    }

    // Mensajes personalizados de error
    // Se envían al cliente cuando una validación falla
    public function messages(): array
    {
        return [

            // Mensajes para email
            'email.required' => 'El correo electrónico es obligatorio.',
            'email.email'    => 'El correo electrónico no es válido.',
            'email.max'      => 'El correo electrónico es demasiado largo.',

            // Mensajes para password
            // Nota: aquí se usan mensajes genéricos para no dar pistas de seguridad
            'password.required' => 'La contraseña es obligatoria.',
            'password.min'      => 'La contraseña es incorrecta.',
            'password.max'      => 'La contraseña es incorrecta.',
        ];
    }
}
