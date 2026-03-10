<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ContratoRequest extends FormRequest
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
      $ismethodPut = $this->isMethod('put') || $this->isMethod('patch');
      
        return [
            'tipo_contrato' => $ismethodPut
                ? 'bail|sometimes|required|string|max:150'
                : 'bail|required|string|max:150',

            'Cod_empleado' => $ismethodPut
                ? 'bail|sometimes|required|exists:Empleado,Cod_empleado'
                : 'bail|required|exists:Empleado,Cod_empleado',

            'forma_de_pago' => $ismethodPut
                ? 'bail|sometimes|required|string|max:150'
                : 'bail|required|string|max:150',

            'fecha_ingreso' => $ismethodPut
                ? 'bail|sometimes|required|date'
                : 'bail|required|date',

            'fecha_fin' => $ismethodPut
                ? 'bail|sometimes|nullable|date|after_or_equal:fecha_ingreso'
                : 'bail|nullable|date|after_or_equal:fecha_ingreso',

            'salario_base' => $ismethodPut
                ? 'bail|sometimes|required|integer|min:0'
                : 'bail|required|integer|min:0',

            'Cod_cargo' => $ismethodPut
                ? 'bail|sometimes|required|exists:Cargo,Cod_cargo'
                : 'bail|required|exists:Cargo,Cod_cargo',

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
    
                'Cod_empleado.required' => 'El código del empleado es obligatorio.',
                'Cod_empleado.exists' => 'El código del empleado no existe en la base de datos.',
    
                'forma_de_pago.required' => 'La forma de pago es obligatoria.',
                'forma_de_pago.string' => 'La forma de pago debe ser una cadena de texto.',
                'forma_de_pago.max' => 'La forma de pago no puede exceder los 150 caracteres.',
    
                'fecha_ingreso.required' => 'La fecha de ingreso es obligatoria.',
                'fecha_ingreso.date' => 'La fecha de ingreso debe ser una fecha válida.',
    
                'fecha_fin.date' => 'La fecha de fin debe ser una fecha válida.',
                'fecha_fin.after_or_equal' => 'La fecha de fin debe ser igual o posterior a la fecha de ingreso.',
    
                'salario_base.required' => 'El salario base es obligatorio.',
                'salario_base.integer' => 'El salario base debe ser un número entero.',
                'salario_base.min' => 'El salario base no puede ser negativo.',
    
                'Cod_cargo.required' => 'El código del cargo es obligatorio.',
                'Cod_cargo.exists' => 'El código del cargo no existe en la base de datos.',
    
                'modalidad_trabajo.required' => 'La modalidad de trabajo es obligatoria.',
                'modalidad_trabajo.string' => 'La modalidad de trabajo debe ser una cadena de texto.',
                'modalidad_trabajo.max' => 'La modalidad de trabajo no puede exceder los 150 caracteres.',
    
                'horario_trabajo.required' => 'El horario de trabajo es obligatorio.',
                'horario_trabajo.string' => 'El horario de trabajo debe ser una cadena de texto.',
                'horario_trabajo.max' => 'El horario de trabajo no puede exceder los 150 caracteres.',
    
                'auxilio_transporte.required' => 'El auxilio de transporte es obligatorio.',
                'auxilio_transporte.boolean' => 'El auxilio de transporte debe ser un valor booleano.',
            ];
        
        }
}