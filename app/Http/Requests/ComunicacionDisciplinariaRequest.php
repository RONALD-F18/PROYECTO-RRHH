<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\Validator;
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
            'llamado de atencion escrito' => 'MEMORANDO',
            'llamado de atencion' => 'LLAMADO_VERBAL',
            'llamado verbal' => 'LLAMADO_VERBAL',
            'memorando' => 'MEMORANDO',
            'apercibimiento formal' => 'MEMORANDO',
            'compromiso de mejora' => 'LLAMADO_VERBAL',
            'suspension' => 'MEMORANDO',
            'suspensión' => 'MEMORANDO',
            'suspension disciplinaria' => 'MEMORANDO',
            'suspensión disciplinaria' => 'MEMORANDO',
            'felicitacion' => 'FELICITACION',
            'felicitación' => 'FELICITACION',
        ];

        if ($this->has('tipo_comunicacion') && is_string($this->tipo_comunicacion)) {
            $raw = trim($this->tipo_comunicacion);
            $normalizado = mb_strtolower($raw);
            if (isset($mapTipos[$normalizado])) {
                $this->merge(['tipo_comunicacion' => $mapTipos[$normalizado]]);
            } else {
                $api = strtoupper(str_replace(' ', '_', $normalizado));
                if (in_array($api, config('rrhh.tipos_comunicacion'), true)) {
                    $this->merge(['tipo_comunicacion' => $api]);
                }
            }
        }

        $mapEstados = [
            'emitida' => 'EMITIDO',
            'emitido' => 'EMITIDO',
            'borrador' => 'EMITIDO',
            'en seguimiento' => 'EMITIDO',
            'activa' => 'EMITIDO',
            'cerrada' => 'NOTIFICADO',
            'notificado' => 'NOTIFICADO',
        ];

        if ($this->has('estado_comunicacion') && is_string($this->estado_comunicacion)) {
            $raw = trim($this->estado_comunicacion);
            $normalizado = mb_strtolower($raw);
            if (isset($mapEstados[$normalizado])) {
                $this->merge(['estado_comunicacion' => $mapEstados[$normalizado]]);
            } else {
                $api = strtoupper($normalizado);
                if (in_array($api, config('rrhh.estados_comunicacion'), true)) {
                    $this->merge(['estado_comunicacion' => $api]);
                }
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
                ? 'bail|sometimes|required|string|max:20'
                : 'bail|required|string|max:20',
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

    public function withValidator(Validator $validator): void
    {
        $validator->after(function (Validator $v): void {
            if ($v->errors()->isNotEmpty()) {
                return;
            }

            $tipo = $this->input('tipo_comunicacion');
            if ($tipo !== 'MEMORANDO') {
                return;
            }

            if (! $this->filled('fecha_inicio_suspension')) {
                $v->errors()->add('fecha_inicio_suspension', 'El memorando debe incluir la fecha de inicio de la suspensión.');
            }
            if (! $this->filled('fecha_fin_suspension')) {
                $v->errors()->add('fecha_fin_suspension', 'El memorando debe incluir la fecha de fin de la suspensión.');
            }

            $dias = $this->input('dias_suspension');
            if ($dias === null || (int) $dias < 1) {
                $v->errors()->add('dias_suspension', 'El memorando debe incluir al menos 1 día de suspensión.');
            }
        });
    }

    public function messages(): array
    {
        return [
            'tipo_comunicacion.required' => 'El tipo de comunicación es obligatorio.',
            'tipo_comunicacion.in' => 'El tipo debe ser: Llamado verbal, Memorando o Felicitación.',
            'fecha_emision.required' => 'La fecha de emisión es obligatoria.',
            'fecha_emision.date' => 'La fecha de emisión debe ser una fecha válida.',
            'fecha_inicio_suspension.date' => 'La fecha de inicio de suspensión debe ser una fecha válida.',
            'fecha_fin_suspension.date' => 'La fecha de fin de suspensión debe ser una fecha válida.',
            'fecha_fin_suspension.after_or_equal' => 'La fecha fin de suspensión debe ser igual o posterior al inicio.',
            'estado_comunicacion.required' => 'El estado de la comunicación es obligatorio.',
            'estado_comunicacion.in' => 'El estado debe ser: Emitido o Notificado.',
            'motivo_comunicacion.required' => 'El motivo de la comunicación es obligatorio.',
            'motivo_comunicacion.max' => 'El motivo no puede superar 20 caracteres.',
            'descripcion.string' => 'La descripción debe ser una cadena de texto.',
            'dias_suspension.integer' => 'Los días de suspensión deben ser un número entero.',
            'dias_suspension.min' => 'Los días de suspensión no pueden ser negativos.',
            'cod_empleado.required' => 'El código del empleado es obligatorio.',
            'cod_empleado.exists' => 'El código del empleado no existe en la base de datos.',
        ];
    }
}
