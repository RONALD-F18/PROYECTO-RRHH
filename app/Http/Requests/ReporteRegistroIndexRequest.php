<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ReporteRegistroIndexRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'modulo' => ['sometimes', 'nullable', 'string', 'in:empleados,contratos,prestaciones,incapacidades,inasistencias,afiliaciones,disciplinario'],
            'fecha_desde' => ['sometimes', 'nullable', 'date'],
            'fecha_hasta' => ['sometimes', 'nullable', 'date', 'after_or_equal:fecha_desde'],
        ];
    }
}
