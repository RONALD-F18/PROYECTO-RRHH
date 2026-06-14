<?php

namespace App\Rules;

use Closure;
use Illuminate\Contracts\Validation\ValidationRule;

class ContrasenaSegura implements ValidationRule
{
    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        if (! is_string($value)) {
            $fail('La contraseña debe ser texto.');

            return;
        }

        if (! preg_match('/[A-Z]/', $value)) {
            $fail('La contraseña debe incluir al menos una letra mayúscula.');
        }

        if (! preg_match('/[a-z]/', $value)) {
            $fail('La contraseña debe incluir al menos una letra minúscula.');
        }

        if (! preg_match('/[0-9]/', $value)) {
            $fail('La contraseña debe incluir al menos un número.');
        }

        if (! preg_match('/[^A-Za-z0-9]/', $value)) {
            $fail('La contraseña debe incluir al menos un carácter especial (ej. @, #, !).');
        }
    }
}
