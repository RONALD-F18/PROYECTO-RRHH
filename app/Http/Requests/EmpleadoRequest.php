<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class EmpleadoRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $isMethodPut = $this->isMethod('put') || $this->isMethod('patch');

        return [
            'nombre_empleado' => $isMethodPut
                ? 'bail|sometimes|required|string|max:100|regex:/^[\pL\s]+$/u'
                : 'bail|required|string|max:100|regex:/^[\pL\s]+$/u',

            'apellidos_empleado' => $isMethodPut
                ? 'bail|sometimes|required|string|max:100|regex:/^[\pL\s]+$/u'
                : 'bail|required|string|max:100|regex:/^[\pL\s]+$/u',

            'doc_iden' => $isMethodPut
                ? 'bail|sometimes|required|string|max:20|unique:empleados,doc_iden,' . $this->cod_empleado . ',cod_empleado'
                : 'bail|required|string|max:20|unique:empleados,doc_iden',

            'tipo_documento' => $isMethodPut
                ? 'bail|sometimes|required|string|in:CC,CE,TI,PASAPORTE'
                : 'bail|required|string|in:CC,CE,TI,PASAPORTE',

            'fecha_nac' => $isMethodPut
                ? 'bail|sometimes|required|date|before:today'
                : 'bail|required|date|before:today',

            'direccion' => $isMethodPut
                ? 'bail|sometimes|required|string|max:200'
                : 'bail|required|string|max:200',

            'numero_telefono' => $isMethodPut
                ? 'bail|sometimes|required|string|regex:/^3[0-9]{9}$/|unique:empleados,numero_telefono,' . $this->cod_empleado . ',cod_empleado'
                : 'bail|required|string|regex:/^3[0-9]{9}$/|unique:empleados,numero_telefono',

            'numero_cuenta' => $isMethodPut
                ? 'bail|sometimes|required|string|regex:/^[0-9]{8,20}$/|unique:empleados,numero_cuenta,' . $this->cod_empleado . ',cod_empleado'
                : 'bail|required|string|regex:/^[0-9]{8,20}$/|unique:empleados,numero_cuenta',

            'tipo_cuenta' => $isMethodPut
                ? 'bail|sometimes|required|string|in:AHORROS,CORRIENTE'
                : 'bail|required|string|in:AHORROS,CORRIENTE',

            'cod_banco' => $isMethodPut
                ? 'bail|sometimes|required|exists:bancos,cod_banco'
                : 'bail|required|exists:bancos,cod_banco',

            'estado_emp' => $isMethodPut
                ? 'bail|sometimes|required|string|in:ACTIVO,INACTIVO'
                : 'bail|nullable|string|in:ACTIVO,INACTIVO',

            'discapacidad' => $isMethodPut
                ? 'bail|sometimes|required|string|in:NINGUNA,VISUAL,AUDITIVA,MOTORA,COGNITIVA'
                : 'bail|required|string|in:NINGUNA,VISUAL,AUDITIVA,MOTORA,COGNITIVA',

            'nacionalidad' => $isMethodPut
                ? 'bail|sometimes|required|string|max:50|regex:/^[\pL\s]+$/u'
                : 'bail|required|string|max:50|regex:/^[\pL\s]+$/u',

            'estado_civil' => $isMethodPut
                ? 'bail|sometimes|required|string|in:SOLTERO,CASADO,VIUDO,DIVORCIADO,UNION_LIBRE'
                : 'bail|required|string|in:SOLTERO,CASADO,VIUDO,DIVORCIADO,UNION_LIBRE',

            'grupo_sanguineo' => $isMethodPut
                ? 'bail|sometimes|required|string|in:A+,A-,B+,B-,AB+,AB-,O+,O-'
                : 'bail|required|string|in:A+,A-,B+,B-,AB+,AB-,O+,O-',

            'profesion' => $isMethodPut
                ? 'bail|sometimes|required|string|max:100|regex:/^[\pL\s]+$/u'
                : 'bail|required|string|max:100|regex:/^[\pL\s]+$/u',

            'fec_exp_doc' => $isMethodPut
                ? 'bail|sometimes|required|date|after:fecha_nac|before_or_equal:today'
                : 'bail|required|date|after:fecha_nac|before_or_equal:today',

            'descripcion' => $isMethodPut
                ? 'bail|sometimes|required|string|max:500'
                : 'bail|required|string|max:500',
        ];
    }

    public function messages(): array
    {
        return [
            'nombre_empleado.required' => 'El nombre del empleado es obligatorio.',
            'nombre_empleado.regex' => 'El nombre solo puede contener letras y espacios.',

            'apellidos_empleado.required' => 'Los apellidos del empleado son obligatorios.',
            'apellidos_empleado.regex' => 'Los apellidos solo pueden contener letras y espacios.',

            'doc_iden.required' => 'El documento de identidad es obligatorio.',
            'doc_iden.unique' => 'Este documento ya está registrado.',

            'tipo_documento.required' => 'El tipo de documento es obligatorio.',
            'tipo_documento.in' => 'El tipo de documento debe ser CC, CE, TI o PASAPORTE.',

            'fecha_nac.required' => 'La fecha de nacimiento es obligatoria.',
            'fecha_nac.before' => 'La fecha de nacimiento no puede ser futura.',

            'direccion.required' => 'La dirección es obligatoria.',

            'numero_telefono.required' => 'El número de teléfono es obligatorio.',
            'numero_telefono.regex' => 'El teléfono debe ser un celular colombiano válido (10 dígitos).',
            'numero_telefono.unique' => 'Este número ya está registrado.',

            'numero_cuenta.required' => 'El número de cuenta es obligatorio.',
            'numero_cuenta.regex' => 'El número de cuenta debe contener solo números.',
            'numero_cuenta.unique' => 'Este número de cuenta ya está registrado.',

            'tipo_cuenta.required' => 'El tipo de cuenta es obligatorio.',
            'tipo_cuenta.in' => 'El tipo de cuenta debe ser AHORROS o CORRIENTE.',

            'cod_banco.required' => 'El banco es obligatorio.',
            'cod_banco.exists' => 'El banco seleccionado no existe.',

            'estado_emp.required' => 'El estado del empleado es obligatorio.',
            'estado_emp.in' => 'El estado debe ser ACTIVO o INACTIVO.',

            'discapacidad.required' => 'El campo discapacidad es obligatorio.',
            'discapacidad.in' => 'La discapacidad debe ser NINGUNA, VISUAL, AUDITIVA, MOTORA o COGNITIVA.',

            'nacionalidad.required' => 'La nacionalidad es obligatoria.',
            'nacionalidad.regex' => 'La nacionalidad solo puede contener letras y espacios.',

            'estado_civil.required' => 'El estado civil es obligatorio.',
            'estado_civil.in' => 'El estado civil debe ser SOLTERO, CASADO, VIUDO, DIVORCIADO o UNION_LIBRE.',

            'grupo_sanguineo.required' => 'El grupo sanguíneo es obligatorio.',
            'grupo_sanguineo.in' => 'El grupo sanguíneo debe ser A+, A-, B+, B-, AB+, AB-, O+ o O-.',

            'profesion.required' => 'La profesión es obligatoria.',
            'profesion.regex' => 'La profesión solo puede contener letras y espacios.',

            'fec_exp_doc.required' => 'La fecha de expedición del documento es obligatoria.',
            'fec_exp_doc.after' => 'La fecha de expedición debe ser posterior a la fecha de nacimiento.',
            'fec_exp_doc.before_or_equal' => 'La fecha de expedición no puede ser futura.',

            'descripcion.required' => 'La descripción es obligatoria.',
        ];
    }
}