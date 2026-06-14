<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class ComunicacionDisciplinariaRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    protected function prepareForValidation(): void
    {
        $mapTipos = [
            'llamado de atencion escrito' => 'Memorando',
            'llamado de atencion' => 'Memorando',
            'memorando' => 'Memorando',
        ];

        if ($this->has('tipo_comunicacion') && is_string($this->tipo_comunicacion)) {
            $normalizado = mb_strtolower(trim($this->tipo_comunicacion));
            if (isset($mapTipos[$normalizado])) {
                $this->merge(['tipo_comunicacion' => $mapTipos[$normalizado]]);
            }
        }
    }

    public function rules(): array
    {
        $isUpdate = $this->isMethod('put') || $this->isMethod('patch');

        return [
            'tipo_comunicacion' => $isUpdate
                ? ['bail', 'sometimes', 'required', 'string', 'max:50', Rule::in(config('rrhh.tipos_comunicacion'))]
                : ['bail', 'required', 'string', 'max:50', Rule::in(config('rrhh.tipos_comunicacion'))],
            'fecha_emision' => $isUpdate
                ? 'bail|sometimes|required|date'
                : 'bail|required|date',
            'fecha_inicio_suspension' => $isUpdate
                ? 'bail|sometimes|nullable|date'
                : 'bail|nullable|date',
            'fecha_fin_suspension' => $isUpdate
                ? 'bail|sometimes|nullable|date|after_or_equal:fecha_inicio_suspension'
                : 'bail|nullable|date|after_or_equal:fecha_inicio_suspension',
            'estado_comunicacion' => $isUpdate
                ? ['bail', 'sometimes', 'required', 'string', 'max:20', Rule::in(config('rrhh.estados_comunicacion'))]
                : ['bail', 'required', 'string', 'max:20', Rule::in(config('rrhh.estados_comunicacion'))],
            'motivo_comunicacion' => $isUpdate
                ? ['bail', 'sometimes', 'required', 'string', 'max:20', Rule::in(config('rrhh.motivos_comunicacion'))]
                : ['bail', 'required', 'string', 'max:20', Rule::in(config('rrhh.motivos_comunicacion'))],
            'descripcion' => $isUpdate
                ? 'bail|sometimes|nullable|string'
                : 'bail|nullable|string',
            'dias_suspension' => $isUpdate
                ? 'bail|sometimes|nullable|integer|min:0'
                : 'bail|nullable|integer|min:0',
            'cod_empleado' => $isUpdate
                ? 'bail|sometimes|required|integer|exists:empleados,cod_empleado'
                : 'bail|required|integer|exists:empleados,cod_empleado',
        ];
    }

    public function messages(): array
    {
        return [
            'tipo_comunicacion.required' => 'El tipo de comunicación es obligatorio.',
            'tipo_comunicacion.in' => 'El tipo debe ser: '.implode(', ', config('rrhh.tipos_comunicacion')).'.',
            'fecha_emision.required' => 'La fecha de emisión es obligatoria.',
            'fecha_emision.date' => 'La fecha de emisión debe ser una fecha válida.',
            'fecha_inicio_suspension.date' => 'La fecha de inicio de suspensión debe ser una fecha válida.',
            'fecha_fin_suspension.date' => 'La fecha de fin de suspensión debe ser una fecha válida.',
            'fecha_fin_suspension.after_or_equal' => 'La fecha fin de suspensión debe ser igual o posterior al inicio.',
            'estado_comunicacion.required' => 'El estado de la comunicación es obligatorio.',
            'estado_comunicacion.in' => 'El estado debe ser: '.implode(', ', config('rrhh.estados_comunicacion')).'.',
            'motivo_comunicacion.required' => 'El motivo de la comunicación es obligatorio.',
            'motivo_comunicacion.in' => 'El motivo debe ser: '.implode(', ', config('rrhh.motivos_comunicacion')).'.',
            'descripcion.string' => 'La descripción debe ser una cadena de texto.',
            'dias_suspension.integer' => 'Los días de suspensión deben ser un número entero.',
            'dias_suspension.min' => 'Los días de suspensión no pueden ser negativos.',
            'cod_empleado.required' => 'El código del empleado es obligatorio.',
            'cod_empleado.exists' => 'El código del empleado no existe en la base de datos.',
        ];
    }
}
