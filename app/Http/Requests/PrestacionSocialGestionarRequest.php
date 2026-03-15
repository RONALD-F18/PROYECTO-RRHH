<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class PrestacionSocialGestionarRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'cod_prestacion_social_periodo' => 'bail|required|integer|min:1|exists:prestacion_social_periodo,cod_prestacion_social_periodo',
            'estado_pago' => 'bail|required|string|in:Pagado,Trasladado',
        ];
    }

    public function messages(): array
    {
        return [
            'cod_prestacion_social_periodo.required' => 'El código del período de prestación es obligatorio.',
            'cod_prestacion_social_periodo.integer' => 'El código del período debe ser un número entero.',
            'cod_prestacion_social_periodo.min' => 'El código del período no es válido.',
            'cod_prestacion_social_periodo.exists' => 'El período de prestación no existe.',
            'estado_pago.required' => 'El estado de pago es obligatorio.',
            'estado_pago.string' => 'El estado de pago debe ser un texto.',
            'estado_pago.in' => 'El estado de pago debe ser Pagado o Trasladado.',
        ];
    }
}
