<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class IncapacidadRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $isUpdate = $this->isMethod('put') || $this->isMethod('patch');

        return [
            'descripcion' => $isUpdate ? 'bail|sometimes|nullable|string|max:200' : 'nullable|string|max:200',
            'fecha_inicio' => $isUpdate ? 'bail|sometimes|required|date' : 'required|date',
            'fecha_fin' => $isUpdate ? 'bail|sometimes|required|date|after_or_equal:fecha_inicio' : 'required|date|after_or_equal:fecha_inicio',
            'fecha_radicacion' => 'nullable|date',
            'cod_tipo_incapacidad' => $isUpdate ? 'bail|sometimes|required|integer|exists:tipo_incapacidad,cod_tipo_incapacidad' : 'required|integer|exists:tipo_incapacidad,cod_tipo_incapacidad',
            'cod_empleado' => $isUpdate ? 'bail|sometimes|required|integer|exists:empleados,cod_empleado' : 'required|integer|exists:empleados,cod_empleado',
            'cod_clasificacion_enfermedad' => 'nullable|integer|exists:clasificacion_enfermedad,cod_clasificacion_enfermedad',
            'estado_incapacidad' => $isUpdate ? 'bail|sometimes|nullable|string|max:25|in:Activa,Finalizada,Cancelada' : 'nullable|string|max:25|in:Activa,Finalizada,Cancelada',
        ];
    }

    public function messages(): array
    {
        return [
            'fecha_inicio.required' => 'La fecha de inicio es obligatoria.',
            'fecha_fin.required' => 'La fecha de fin es obligatoria.',
            'fecha_fin.after_or_equal' => 'La fecha de fin debe ser igual o posterior a la fecha de inicio.',
            'cod_tipo_incapacidad.required' => 'El tipo de incapacidad es obligatorio.',
            'cod_tipo_incapacidad.exists' => 'El tipo de incapacidad no existe.',
            'cod_empleado.required' => 'El empleado es obligatorio.',
            'cod_empleado.exists' => 'El empleado no existe.',
        ];
    }
}
