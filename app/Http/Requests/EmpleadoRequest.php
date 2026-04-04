<?php

namespace App\Http\Requests;

use App\Models\Empleado;
use Carbon\Carbon;
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
        $trimKeys = [
            'nombre_empleado', 'apellidos_empleado', 'direccion', 'profesion', 'nacionalidad',
            'descripcion', 'doc_iden', 'correo_empleado', 'numero_telefono', 'numero_cuenta',
        ];
        $trimmed = [];
        foreach ($trimKeys as $key) {
            if ($this->has($key) && is_string($this->input($key))) {
                $trimmed[$key] = trim($this->input($key));
            }
        }
        if ($trimmed !== []) {
            $this->merge($trimmed);
        }

        if (! $this->isMethod('put') && ! $this->isMethod('patch')) {
            return;
        }

        $cod = $this->route('empleado');
        if (! $cod) {
            return;
        }

        $empleado = Empleado::query()->find($cod);
        if (! $empleado) {
            return;
        }

        $merge = [];
        if (! $this->filled('fecha_nac') && $empleado->fecha_nac) {
            $fn = $empleado->fecha_nac;
            $merge['fecha_nac'] = $fn instanceof \DateTimeInterface
                ? $fn->format('Y-m-d')
                : (string) $fn;
        }
        if (! $this->filled('tipo_documento') && $empleado->tipo_documento) {
            $merge['tipo_documento'] = $empleado->tipo_documento;
        }
        if (! $this->filled('fec_exp_doc') && $empleado->fec_exp_doc) {
            $fe = $empleado->fec_exp_doc;
            $merge['fec_exp_doc'] = $fe instanceof \DateTimeInterface
                ? $fe->format('Y-m-d')
                : (string) $fe;
        }
        if ($merge !== []) {
            $this->merge($merge);
        }
    }

    public function rules(): array
    {
        $isMethodPut = $this->isMethod('put') || $this->isMethod('patch');

        $docIgnoreId = $this->route('empleado');

        $limite120 = now()->subYears(120)->format('Y-m-d');
        $edadMinima = max(15, (int) config('rrhh.empleado_edad_minima', 15));
        $fechaTopeEdadMin = now()->subYears($edadMinima)->format('Y-m-d');
        $fechaTopeMayoria18 = now()->subYears(18)->format('Y-m-d');

        $fecExpRules = [
            'bail',
            $isMethodPut ? 'sometimes' : null,
            $isMethodPut ? 'required' : 'required',
            'date',
            'after:fecha_nac',
            'before_or_equal:today',
            Rule::when(
                fn () => $this->input('tipo_documento') === 'CC' && $this->filled('fecha_nac'),
                [
                    function (string $attribute, mixed $value, \Closure $fail): void {
                        try {
                            $nac = Carbon::parse($this->input('fecha_nac'));
                            $exp = Carbon::parse($value);
                            if ($exp->lt($nac->copy()->addYearsNoOverflow(18))) {
                                $fail('Con cédula de ciudadanía (CC), la expedición debe ser en o después de cumplir 18 años (Decreto 1260 de 1970 y normas de identificación vigentes).');
                            }
                        } catch (\Throwable) {
                            $fail('Las fechas de nacimiento y expedición no son válidas.');
                        }
                    },
                ]
            ),
        ];

        $fecExpRules = array_values(array_filter($fecExpRules, fn ($r) => $r !== null));

        return [
            'nombre_empleado' => $isMethodPut
                ? 'bail|sometimes|required|string|min:2|max:100|regex:/^[\pL\s]+$/u'
                : 'bail|required|string|min:2|max:100|regex:/^[\pL\s]+$/u',

            'apellidos_empleado' => $isMethodPut
                ? 'bail|sometimes|required|string|min:2|max:100|regex:/^[\pL\s]+$/u'
                : 'bail|required|string|min:2|max:100|regex:/^[\pL\s]+$/u',

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

            'fecha_nac' => array_values(array_filter([
                'bail',
                $isMethodPut ? 'sometimes' : null,
                $isMethodPut ? 'required' : 'required',
                'date',
                'before:today',
                'after_or_equal:'.$limite120,
                Rule::when(
                    fn () => ! in_array($this->input('tipo_documento'), ['CC', 'TI'], true),
                    ['before_or_equal:'.$fechaTopeEdadMin]
                ),
                Rule::when(
                    fn () => $this->input('tipo_documento') === 'CC',
                    ['before_or_equal:'.$fechaTopeMayoria18]
                ),
                Rule::when(
                    fn () => $this->input('tipo_documento') === 'TI',
                    [
                        'before_or_equal:'.$fechaTopeEdadMin,
                        'after:'.$fechaTopeMayoria18,
                    ]
                ),
            ], fn ($r) => $r !== null)),

            'direccion' => $isMethodPut
                ? 'bail|sometimes|required|string|min:10|max:200'
                : 'bail|required|string|min:10|max:200',

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
                ? 'bail|sometimes|required|string|min:3|max:50|regex:/^[\pL\s]+$/u'
                : 'bail|required|string|min:3|max:50|regex:/^[\pL\s]+$/u',

            'estado_civil' => $isMethodPut
                ? 'bail|sometimes|required|string|in:SOLTERO,CASADO,VIUDO,DIVORCIADO,UNION_LIBRE'
                : 'bail|required|string|in:SOLTERO,CASADO,VIUDO,DIVORCIADO,UNION_LIBRE',

            'grupo_sanguineo' => $isMethodPut
                ? 'bail|sometimes|required|string|in:A+,A-,B+,B-,AB+,AB-,O+,O-'
                : 'bail|required|string|in:A+,A-,B+,B-,AB+,AB-,O+,O-',

            'profesion' => $isMethodPut
                ? 'bail|sometimes|required|string|min:2|max:100|regex:/^[\pL\s]+$/u'
                : 'bail|required|string|min:2|max:100|regex:/^[\pL\s]+$/u',

            'fec_exp_doc' => $fecExpRules,

            'descripcion' => $isMethodPut
                ? 'bail|sometimes|nullable|string|max:500'
                : 'bail|nullable|string|max:500',
        ];
    }

    public function messages(): array
    {
        $edadMin = max(15, (int) config('rrhh.empleado_edad_minima', 15));

        return [
            'nombre_empleado.required' => 'El nombre del empleado es obligatorio.',
            'nombre_empleado.min' => 'El nombre debe tener al menos 2 caracteres.',
            'nombre_empleado.regex' => 'El nombre solo puede contener letras y espacios.',

            'apellidos_empleado.required' => 'Los apellidos del empleado son obligatorios.',
            'apellidos_empleado.min' => 'Los apellidos deben tener al menos 2 caracteres.',
            'apellidos_empleado.regex' => 'Los apellidos solo pueden contener letras y espacios.',

            'doc_iden.required' => 'El documento de identidad es obligatorio.',
            'doc_iden.unique' => 'Este documento ya está registrado.',
            'doc_iden.regex' => 'El documento no cumple el formato según el tipo seleccionado (CC/CE/TI: 5 a 10 dígitos; PASAPORTE: 3 a 50 caracteres alfanuméricos y guion).',

            'tipo_documento.required' => 'El tipo de documento es obligatorio.',
            'tipo_documento.in' => 'El tipo de documento debe ser CC, CE, TI o PASAPORTE.',

            'fecha_nac.required' => 'La fecha de nacimiento es obligatoria.',
            'fecha_nac.before' => 'La fecha de nacimiento no puede ser hoy ni una fecha futura.',
            'fecha_nac.before_or_equal' => "La fecha de nacimiento no es coherente con el tipo de documento y la edad: mínimo {$edadMin} años para vínculo laboral; con CC debe ser mayor de edad (18+); con TI debe ser menor de 18 y al menos {$edadMin} años.",
            'fecha_nac.after' => 'Con tarjeta de identidad (TI) el titular debe ser menor de 18 años.',
            'fecha_nac.after_or_equal' => 'La fecha de nacimiento no puede indicar una edad mayor a 120 años.',

            'direccion.required' => 'La dirección es obligatoria.',
            'direccion.min' => 'Indique una dirección completa (mínimo 10 caracteres), acorde a registros de ubicación del trabajador.',

            'numero_telefono.required' => 'El número de teléfono es obligatorio.',
            'numero_telefono.regex' => 'El teléfono debe ser un celular colombiano válido (10 dígitos, inicia en 3).',
            'numero_telefono.unique' => 'Este número ya está registrado.',

            'correo_empleado.required' => 'El correo del empleado es obligatorio.',
            'correo_empleado.email' => 'El correo del empleado no tiene un formato válido o el dominio no existe.',
            'correo_empleado.regex' => 'El correo del empleado contiene un dominio inválido (ejemplo no válido: usuario@gmail..com).',
            'correo_empleado.unique' => 'Este correo del empleado ya está registrado.',

            'numero_cuenta.required' => 'El número de cuenta es obligatorio.',
            'numero_cuenta.regex' => 'El número de cuenta debe contener solo números (8 a 20 dígitos).',
            'numero_cuenta.unique' => 'Este número de cuenta ya está registrado.',

            'tipo_cuenta.required' => 'El tipo de cuenta es obligatorio.',
            'tipo_cuenta.in' => 'El tipo de cuenta debe ser AHORROS o CORRIENTE.',

            'cod_banco.exists' => 'El banco seleccionado no existe.',

            'estado_emp.in' => 'El estado debe ser ACTIVO o INACTIVO.',

            'discapacidad.required' => 'El campo discapacidad es obligatorio.',
            'discapacidad.in' => 'La discapacidad debe ser NINGUNA, VISUAL, AUDITIVA, MOTORA o COGNITIVA.',

            'nacionalidad.required' => 'La nacionalidad es obligatoria.',
            'nacionalidad.min' => 'La nacionalidad debe tener al menos 3 caracteres.',
            'nacionalidad.regex' => 'La nacionalidad solo puede contener letras y espacios.',

            'estado_civil.required' => 'El estado civil es obligatorio.',
            'estado_civil.in' => 'El estado civil debe ser SOLTERO, CASADO, VIUDO, DIVORCIADO o UNION_LIBRE.',

            'grupo_sanguineo.required' => 'El grupo sanguíneo es obligatorio.',
            'grupo_sanguineo.in' => 'El grupo sanguíneo debe ser A+, A-, B+, B-, AB+, AB-, O+ o O-.',

            'profesion.required' => 'La profesión es obligatoria.',
            'profesion.min' => 'La profesión debe tener al menos 2 caracteres.',
            'profesion.regex' => 'La profesión solo puede contener letras y espacios.',

            'fec_exp_doc.required' => 'La fecha de expedición del documento es obligatoria.',
            'fec_exp_doc.after' => 'La fecha de expedición debe ser posterior a la fecha de nacimiento.',
            'fec_exp_doc.before_or_equal' => 'La fecha de expedición no puede ser una fecha futura; use la fecha de emisión del documento.',
        ];
    }
}
