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
                ? 'bail|sometimes|required|string|max:100|regex:/^[a-zA-Z\s]+$/'
                : 'bail|required|string|max:100|regex:/^[a-zA-Z\s]+$/',

            'apellidos_empleado' => $isMethodPut
                ? 'bail|sometimes|required|string|max:100|regex:/^[a-zA-Z\s]+$/'
                : 'bail|required|string|max:100|regex:/^[a-zA-Z\s]+$/',

            'doc_iden' => $isMethodPut
                ? 'bail|sometimes|required|string|max:50|unique:empleados,doc_iden,' . $this->cod_empleado . ',cod_empleado'
                : 'bail|required|string|max:50|unique:empleados,doc_iden',

            'tipo_documento' => $isMethodPut
                ? 'bail|sometimes|required|string|in:DNI,PASAPORTE,LICENCIA'
                : 'bail|required|string|in:DNI,PASAPORTE,LICENCIA',

            'fecha_nac' => $isMethodPut
                ? 'bail|sometimes|required|date|before:today'
                : 'bail|required|date|before:today',

            'direccion' => $isMethodPut
                ? 'bail|sometimes|required|string|max:200'
                : 'bail|required|string|max:200',

            'numero_telefono' => $isMethodPut
                ? 'bail|sometimes|required|string|max:50|unique:empleados,numero_telefono,' . $this->cod_empleado . ',cod_empleado|regex:/^\+?[0-9]{7,15}$/'
                : 'bail|required|string|max:50|unique:empleados,numero_telefono|regex:/^\+?[0-9]{7,15}$/',

            'numero_cuenta' => $isMethodPut
                ? 'bail|sometimes|required|string|max:50|unique:empleados,numero_cuenta,' . $this->cod_empleado . ',cod_empleado'
                : 'bail|required|string|max:50|unique:empleados,numero_cuenta',

            'tipo_cuenta' => $isMethodPut
                ? 'bail|sometimes|required|string|in:AHORROS,CORRIENTE'
                : 'bail|required|string|in:AHORROS,CORRIENTE',

            'cod_banco' => $isMethodPut
                ? 'bail|sometimes|required|exists:bancos,cod_banco'
                : 'bail|required|exists:bancos,cod_banco',

            'estado_emp' => $isMethodPut
                ? 'bail|sometimes|required|string|in:ACTIVO,INACTIVO'
                : 'bail|required|string|in:ACTIVO,INACTIVO',

            'discapacidad' => $isMethodPut
                ? 'bail|sometimes|required|string|max:50'
                : 'bail|required|string|max:50',

            'nacionalidad' => $isMethodPut
                ? 'bail|sometimes|required|string|max:50|alpha'
                : 'bail|required|string|max:50|alpha',

            'estado_civil' => $isMethodPut
                ? 'bail|sometimes|required|string|in:SOLTERO,CASADO,VIUDO,DIVORCIADO'
                : 'bail|required|string|in:SOLTERO,CASADO,VIUDO,DIVORCIADO',

            'grupo_sanguineo' => $isMethodPut
                ? 'bail|sometimes|required|string|in:A+,A-,B+,B-,AB+,AB-,O+,O-'
                : 'bail|required|string|in:A+,A-,B+,B-,AB+,AB-,O+,O-',

            'profesion' => $isMethodPut
                ? 'bail|sometimes|required|string|max:100|regex:/^[a-zA-Z\s]+$/'
                : 'bail|required|string|max:100|regex:/^[a-zA-Z\s]+$/',

            'fec_exp_doc' => $isMethodPut
                ? 'bail|sometimes|required|date|before_or_equal:today'
                : 'bail|required|date|before_or_equal:today',

            'descripcion' => $isMethodPut
                ? 'bail|sometimes|required|string|max:500'
                : 'bail|required|string|max:500',

            'cod_usuario' => $isMethodPut
                ? 'bail|sometimes|required|exists:usuarios,cod_usuario'
                : 'bail|required|exists:usuarios,cod_usuario',
        ];
    }

    public function messages(): array
    {
        return [
            'nombre_empleado.required' => 'El nombre del empleado es obligatorio.',
            'nombre_empleado.string' => 'El nombre debe ser texto.',
            'nombre_empleado.max' => 'El nombre no puede exceder 100 caracteres.',
            'nombre_empleado.regex' => 'El nombre solo puede contener letras y espacios.',

            'apellidos_empleado.required' => 'Los apellidos del empleado son obligatorios.',
            'apellidos_empleado.string' => 'Los apellidos deben ser texto.',
            'apellidos_empleado.max' => 'Los apellidos no pueden exceder 100 caracteres.',
            'apellidos_empleado.regex' => 'Los apellidos solo pueden contener letras y espacios.',

            'doc_iden.required' => 'El documento de identidad es obligatorio.',
            'doc_iden.unique' => 'Este documento ya está registrado.',
            'doc_iden.max' => 'El documento no puede exceder 50 caracteres.',

            'tipo_documento.required' => 'El tipo de documento es obligatorio.',
            'tipo_documento.in' => 'El tipo de documento debe ser DNI, PASAPORTE o LICENCIA.',

            'fecha_nac.required' => 'La fecha de nacimiento es obligatoria.',
            'fecha_nac.date' => 'La fecha de nacimiento debe ser una fecha válida.',
            'fecha_nac.before' => 'La fecha de nacimiento no puede ser futura.',

            'direccion.required' => 'La dirección es obligatoria.',
            'direccion.max' => 'La dirección no puede exceder 200 caracteres.',

            'numero_telefono.required' => 'El número de teléfono es obligatorio.',
            'numero_telefono.unique' => 'Este número ya está registrado.',
            'numero_telefono.regex' => 'El número de teléfono debe ser válido.',

            'numero_cuenta.required' => 'El número de cuenta es obligatorio.',
            'numero_cuenta.unique' => 'Este número de cuenta ya está registrado.',
            'numero_cuenta.max' => 'El número de cuenta no puede exceder 50 caracteres.',

            'tipo_cuenta.required' => 'El tipo de cuenta es obligatorio.',
            'tipo_cuenta.in' => 'El tipo de cuenta debe ser AHORROS o CORRIENTE.',

            'cod_banco.required' => 'El banco es obligatorio.',
            'cod_banco.exists' => 'El banco seleccionado no existe.',

            'estado_emp.required' => 'El estado del empleado es obligatorio.',
            'estado_emp.in' => 'El estado debe ser ACTIVO o INACTIVO.',

            'discapacidad.required' => 'El campo discapacidad es obligatorio.',
            'discapacidad.max' => 'El campo discapacidad no puede exceder 50 caracteres.',

            'nacionalidad.required' => 'La nacionalidad es obligatoria.',
            'nacionalidad.alpha' => 'La nacionalidad solo puede contener letras.',

            'estado_civil.required' => 'El estado civil es obligatorio.',
            'estado_civil.in' => 'El estado civil debe ser SOLTERO, CASADO, VIUDO o DIVORCIADO.',

            'grupo_sanguineo.required' => 'El grupo sanguíneo es obligatorio.',
            'grupo_sanguineo.in' => 'El grupo sanguíneo debe ser A+, A-, B+, B-, AB+, AB-, O+ o O-.',

            'profesion.required' => 'La profesión es obligatoria.',
            'profesion.max' => 'La profesión no puede exceder 100 caracteres.',
            'profesion.regex' => 'La profesión solo puede contener letras y espacios.',

            'fec_exp_doc.required' => 'La fecha de expedición del documento es obligatoria.',
            'fec_exp_doc.date' => 'La fecha de expedición debe ser válida.',
            'fec_exp_doc.before_or_equal' => 'La fecha de expedición no puede ser futura.',

            'descripcion.required' => 'La descripción es obligatoria.',
            'descripcion.max' => 'La descripción no puede exceder 500 caracteres.',

            'cod_usuario.required' => 'El usuario es obligatorio.',
            'cod_usuario.exists' => 'El usuario seleccionado no existe.',
        ];
    }
}