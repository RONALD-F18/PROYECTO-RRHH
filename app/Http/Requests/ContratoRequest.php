<?php

namespace App\Http\Requests;

use App\Models\Contrato;
use Illuminate\Foundation\Http\FormRequest;

class ContratoRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    protected function prepareForValidation(): void
    {
        if ($this->has('fecha_fin') && $this->fecha_fin === '') {
            $this->merge(['fecha_fin' => null]);
        }

        if ($this->has('estado_contrato') && is_string($this->estado_contrato)) {
            $this->merge(['estado_contrato' => strtoupper($this->estado_contrato)]);
        }

        if (! $this->isMethod('put') && ! $this->isMethod('patch')) {
            return;
        }

        if (! $this->has('fecha_fin') || $this->filled('fecha_ingreso')) {
            return;
        }

        $cod = $this->route('contrato');
        if (! $cod) {
            return;
        }

        $contrato = Contrato::query()->find($cod);
        if ($contrato?->fecha_ingreso) {
            $fi = $contrato->fecha_ingreso;
            $this->merge([
                'fecha_ingreso' => $fi instanceof \DateTimeInterface
                    ? $fi->format('Y-m-d')
                    : (string) $fi,
            ]);
        }
    }

    public function rules(): array
    {
        $ismethodPut = $this->isMethod('put') || $this->isMethod('patch');

        return [
            'tipo_contrato' => $ismethodPut
                ? 'bail|sometimes|required|string|max:150'
                : 'bail|required|string|max:150',

            'cod_empleado' => $ismethodPut
                ? 'bail|sometimes|required|exists:empleados,cod_empleado'
                : 'bail|required|exists:empleados,cod_empleado',

            'forma_de_pago' => $ismethodPut
                ? 'bail|sometimes|required|string|max:150'
                : 'bail|required|string|max:150',

            'fecha_ingreso' => $ismethodPut
                ? 'bail|sometimes|required|date|date_format:Y-m-d'
                : 'bail|required|date|date_format:Y-m-d',

            'fecha_fin' => $ismethodPut
                ? 'bail|sometimes|nullable|date|date_format:Y-m-d|after_or_equal:fecha_ingreso'
                : 'bail|nullable|date|date_format:Y-m-d|after_or_equal:fecha_ingreso',

            'salario_base' => $ismethodPut
                ? 'bail|sometimes|required|numeric|min:0'
                : 'bail|required|numeric|min:0',

            'cod_cargo' => $ismethodPut
                ? 'bail|sometimes|required|exists:cargo,cod_cargo'
                : 'bail|required|exists:cargo,cod_cargo',

            'modalidad_trabajo' => $ismethodPut
                ? 'bail|sometimes|required|string|max:150'
                : 'bail|required|string|max:150',

            'horario_trabajo' => $ismethodPut
                ? 'bail|sometimes|required|string|max:150'
                : 'bail|required|string|max:150',

            'auxilio_transporte' => $ismethodPut
                ? 'bail|sometimes|required|boolean'
                : 'bail|required|boolean',

            'descripcion' => $ismethodPut
                ? 'bail|sometimes|nullable|string'
                : 'bail|nullable|string',

            'estado_contrato' => $ismethodPut
                ? 'bail|sometimes|required|string|in:ACTIVO,INACTIVO'
                : 'bail|required|string|in:ACTIVO,INACTIVO',
        ];
    }

    public function messages(): array
    {
        return [
            'tipo_contrato.required' => 'El tipo de contrato es obligatorio.',
            'tipo_contrato.string' => 'El tipo de contrato debe ser una cadena de texto.',
            'tipo_contrato.max' => 'El tipo de contrato no puede exceder los 150 caracteres.',

            'cod_empleado.required' => 'El código del empleado es obligatorio.',
            'cod_empleado.exists' => 'El código del empleado no existe en la base de datos.',

            'forma_de_pago.required' => 'La forma de pago es obligatoria.',
            'forma_de_pago.string' => 'La forma de pago debe ser una cadena de texto.',
            'forma_de_pago.max' => 'La forma de pago no puede exceder los 150 caracteres.',

            'fecha_ingreso.required' => 'La fecha de ingreso es obligatoria.',
            'fecha_ingreso.date' => 'La fecha de ingreso debe ser una fecha válida.',
            'fecha_ingreso.date_format' => 'La fecha de ingreso debe tener formato YYYY-MM-DD.',

            'fecha_fin.date' => 'La fecha de fin debe ser una fecha válida.',
            'fecha_fin.date_format' => 'La fecha de fin debe tener formato YYYY-MM-DD.',
            'fecha_fin.after_or_equal' => 'La fecha de fin debe ser igual o posterior a la fecha de ingreso.',

            'salario_base.required' => 'El salario base es obligatorio.',
            'salario_base.numeric' => 'El salario base debe ser un número.',
            'salario_base.min' => 'El salario base no puede ser negativo.',

            'cod_cargo.required' => 'El código del cargo es obligatorio.',
            'cod_cargo.exists' => 'El código del cargo no existe en la base de datos.',

            'modalidad_trabajo.required' => 'La modalidad de trabajo es obligatoria.',
            'modalidad_trabajo.string' => 'La modalidad de trabajo debe ser una cadena de texto.',
            'modalidad_trabajo.max' => 'La modalidad de trabajo no puede exceder los 150 caracteres.',

            'horario_trabajo.required' => 'El horario de trabajo es obligatorio.',
            'horario_trabajo.string' => 'El horario de trabajo debe ser una cadena de texto.',
            'horario_trabajo.max' => 'El horario de trabajo no puede exceder los 150 caracteres.',

            'auxilio_transporte.required' => 'El auxilio de transporte es obligatorio.',
            'auxilio_transporte.boolean' => 'El auxilio de transporte debe ser un valor booleano.',

            'estado_contrato.required' => 'El estado del contrato es obligatorio.',
            'estado_contrato.in' => 'El estado del contrato debe ser ACTIVO o INACTIVO.',
        ];
    }
}
