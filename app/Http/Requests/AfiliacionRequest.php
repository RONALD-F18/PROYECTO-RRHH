<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class AfiliacionRequest extends FormRequest
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
       $isMethodPut = $this->isMethod('put') || $this->isMethod('patch');

        return [

    'cod_empleado' => $isMethodPut
        ? 'sometimes|integer|exists:empleados,cod_empleado'
        : 'required|integer|exists:empleados,cod_empleado',

    'cod_arl' => $isMethodPut
        ? 'sometimes|integer|exists:arl,cod_arl'
        : 'required|integer|exists:arl,cod_arl',

    'cod_fondo_pensiones' => $isMethodPut
        ? 'sometimes|integer|exists:fondo_pensiones,cod_fondo_pensiones'
        : 'required|integer|exists:fondo_pensiones,cod_fondo_pensiones',

    'cod_fondo_cesantias' => $isMethodPut
        ? 'sometimes|integer|exists:fondo_cesantias,cod_fondo_cesantias'
        : 'required|integer|exists:fondo_cesantias,cod_fondo_cesantias',

    'cod_caja_compensacion' => $isMethodPut
        ? 'sometimes|integer|exists:caja_compensaciones,cod_caja_compensacion'
        : 'required|integer|exists:caja_compensaciones,cod_caja_compensacion',
];
    }

    public function messages(): array
    {
        return [
            

            'cod_arl.required' => 'El código de la ARL es obligatorio.',
            'cod_arl.integer' => 'El código de la ARL debe ser un número entero.',
            'cod_arl.exists' => 'El código de la ARL no existe en la base de datos.',

            'cod_fondo_pensiones.required' => 'El código del fondo de pensiones es obligatorio.',
            'cod_fondo_pensiones.integer' => 'El código del fondo de pensiones debe ser un número entero.',
            'cod_fondo_pensiones.exists' => 'El código del fondo de pensiones no existe en la base de datos.',

            'cod_fondo_cesantias.required' => 'El código del fondo de cesantías es obligatorio.',
            'cod_fondo_cesantias.integer' => 'El código del fondo de cesantías debe ser un número entero.',
            'cod_fondo_cesantias.exists' => 'El código del fondo de cesantías no existe en la base de datos.',

            'cod_caja_compensacion.required' => 'El código de la caja de compensación es obligatorio.',
            'cod_caja_compensacion.integer' => 'El código de la caja de compensación debe ser un número entero.',
            'cod_caja_compensacion.exists' => 'El código de la caja de compensación no existe en la base de datos.',
        ];
    }
}