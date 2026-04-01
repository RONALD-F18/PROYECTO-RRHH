<?php

namespace App\Http\Requests;

use App\Models\Empleado;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class EmpleadoRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    protected function prepareForValidation(): void
    {
        if (! $this->isMethod('put') && ! $this->isMethod('patch')) {
            return;
        }

        $cod = $this->route('empleado');
        if (! $cod || $this->filled('fecha_nac')) {
            return;
        }

        $empleado = Empleado::query()->find($cod);
        if (! $empleado) {
            return;
        }

        if (! $this->filled('fecha_nac') && $empleado->fecha_nac) {
            $fn = $empleado->fecha_nac;
            $this->merge([
                'fecha_nac' => $fn instanceof \DateTimeInterface
                    ? $fn->format('Y-m-d')
                    : (string) $fn,
            ]);
        }

        if (! $this->filled('tipo_documento') && $empleado->tipo_documento) {
            $this->merge(['tipo_documento' => $empleado->tipo_documento]);
        }
    }

    public function rules(): array
    {
        $isMethodPut = $this->isMethod('put') || $this->isMethod('patch');

        $docIgnoreId = $this->route('empleado');

        $limite120 = now()->subYears(120)->format('Y-m-d');

        return [
            'nombre_empleado' => $isMethodPut
                ? 'bail|sometimes|required|string|max:100|regex:/^[\pL\s]+$/u'
                : 'bail|required|string|max:100|regex:/^[\pL\s]+$/u',

            'apellidos_empleado' => $isMethodPut
                ? 'bail|sometimes|required|string|max:100|regex:/^[\pL\s]+$/u'
                : 'bail|required|string|max:100|regex:/^[\pL\s]+$/u',

            'tipo_documento' => $isMethodPut
                ? 'bail|sometimes|required|string|in:CC,CE,TI,PASAPORTE'
                : 'bail|required|string|in:CC,CE,TI,PASAPORTE',

            'doc_iden' => [
                'bail',
                $isMethodPut ? 'sometimes' : 'required',
                'string',
                $isMethodPut
                    ? Rule::unique('empleados', 'doc_iden')->ignore((string) $docIgnoreId, 'cod_empleado')
                    : Rule::unique('empleados', 'doc_iden'),
                Rule::when(
                    fn () => in_array($this->input('tipo_documento'), ['CC', 'CE', 'TI'], true),
                    ['regex:/^[0-9]{5,10}$/']
                ),
                Rule::when(
                    fn () => $this->input('tipo_documento') === 'PASAPORTE',
                    ['regex:/^[A-Za-z0-9\-]{3,50}$/']
                ),
            ],

            'fecha_nac' => $isMethodPut
                ? [
                    'bail',
                    'sometimes',
                    'required',
                    'date',
                    'before:today',
                    'after_or_equal:'.$limite120,
                ]
                : [
                    'bail',
                    'required',
                    'date',
                    'before:today',
                    'after_or_equal:'.$limite120,
                ],

            'direccion' => $isMethodPut
                ? 'bail|sometimes|required|string|max:200'
                : 'bail|required|string|max:200',

            'numero_telefono' => $isMethodPut
                ? 'bail|sometimes|required|string|regex:/^3[0-9]{9}$/|unique:empleados,numero_telefono,'.$docIgnoreId.',cod_empleado'
                : 'bail|required|string|regex:/^3[0-9]{9}$/|unique:empleados,numero_telefono',

            'correo_empleado' => $isMethodPut
                ? [
                    'bail',
                    'sometimes',
                    'required',
                    'string',
                    'email:rfc,dns',
                    'max:120',
                    'regex:/^(?!.*\.\.)[A-Za-z0-9._%+\-]+@[A-Za-z0-9\-]+(\.[A-Za-z0-9\-]+)+$/',
                    'unique:empleados,correo_empleado,'.$docIgnoreId.',cod_empleado',
                ]
                : [
                    'bail',
                    'required',
                    'string',
                    'email:rfc,dns',
                    'max:120',
                    'regex:/^(?!.*\.\.)[A-Za-z0-9._%+\-]+@[A-Za-z0-9\-]+(\.[A-Za-z0-9\-]+)+$/',
                    'unique:empleados,correo_empleado',
                ],

            'numero_cuenta' => $isMethodPut
                ? 'bail|sometimes|required|string|regex:/^[0-9]{8,20}$/|unique:empleados,numero_cuenta,'.$docIgnoreId.',cod_empleado'
                : 'bail|required|string|regex:/^[0-9]{8,20}$/|unique:empleados,numero_cuenta',

            'tipo_cuenta' => $isMethodPut
                ? 'bail|sometimes|required|string|in:AHORROS,CORRIENTE'
                : 'bail|required|string|in:AHORROS,CORRIENTE',

            'cod_banco' => $isMethodPut
                ? 'bail|nullable|exists:bancos,cod_banco'
                : 'bail|nullable|exists:bancos,cod_banco',

            'estado_emp' => $isMethodPut
                ? 'bail|nullable|string|in:ACTIVO,INACTIVO'
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
                ? 'bail|sometimes|nullable|string|max:500'
                : 'bail|nullable|string|max:500',
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
            'doc_iden.regex' => 'El documento no cumple el formato según el tipo seleccionado (CC/CE/TI: 5 a 10 dígitos; PASAPORTE: 3 a 50 caracteres alfanuméricos y guion).',

            'tipo_documento.required' => 'El tipo de documento es obligatorio.',
            'tipo_documento.in' => 'El tipo de documento debe ser CC, CE, TI o PASAPORTE.',

            'fecha_nac.required' => 'La fecha de nacimiento es obligatoria.',
            'fecha_nac.before' => 'La fecha de nacimiento no puede ser hoy ni una fecha futura.',
            'fecha_nac.after_or_equal' => 'La fecha de nacimiento no puede indicar una edad mayor a 120 años.',

            'direccion.required' => 'La dirección es obligatoria.',

            'numero_telefono.required' => 'El número de teléfono es obligatorio.',
            'numero_telefono.regex' => 'El teléfono debe ser un celular colombiano válido (10 dígitos).',
            'numero_telefono.unique' => 'Este número ya está registrado.',

            'correo_empleado.required' => 'El correo del empleado es obligatorio.',
            'correo_empleado.email' => 'El correo del empleado no tiene un formato válido o el dominio no existe.',
            'correo_empleado.regex' => 'El correo del empleado contiene un dominio inválido (ejemplo no válido: usuario@gmail..com).',
            'correo_empleado.unique' => 'Este correo del empleado ya está registrado.',

            'numero_cuenta.required' => 'El número de cuenta es obligatorio.',
            'numero_cuenta.regex' => 'El número de cuenta debe contener solo números.',
            'numero_cuenta.unique' => 'Este número de cuenta ya está registrado.',

            'tipo_cuenta.required' => 'El tipo de cuenta es obligatorio.',
            'tipo_cuenta.in' => 'El tipo de cuenta debe ser AHORROS o CORRIENTE.',

            'cod_banco.exists' => 'El banco seleccionado no existe.',

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
        ];
    }
}
