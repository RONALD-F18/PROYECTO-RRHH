<?php

namespace App\Http\Requests;

use App\Models\Contrato;
use App\Models\Empleado;
use App\Support\RrhhCatalog;
use App\Support\RrhhDates;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class ContratoRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    protected function prepareForValidation(): void
    {
        foreach ([
            'tipo_contrato' => config('rrhh.tipos_contrato', []),
            'forma_de_pago' => config('rrhh.formas_pago', []),
            'modalidad_trabajo' => config('rrhh.modalidades_trabajo', []),
            'horario_trabajo' => config('rrhh.horarios_trabajo', []),
        ] as $campo => $opciones) {
            if ($this->has($campo) && is_string($this->input($campo))) {
                $normalizado = RrhhCatalog::normalizar($this->input($campo), $opciones);
                if ($normalizado !== null) {
                    $this->merge([$campo => $normalizado]);
                }
            }
        }

        if ($this->has('fecha_fin') && $this->fecha_fin === '') {
            $this->merge(['fecha_fin' => null]);
        }

        if ($this->has('estado_contrato') && is_string($this->estado_contrato)) {
            $v = strtoupper(trim($this->estado_contrato));
            $sinonimosActivo = ['ACTIVO', 'VIGENTE', 'VIGENCIA'];
            $sinonimosFinalizado = ['FINALIZADO', 'INACTIVO', 'TERMINADO', 'TERMINADA', 'SUSPENDIDO', 'CANCELADO'];
            if (in_array($v, $sinonimosActivo, true)) {
                $v = 'ACTIVO';
            } elseif (in_array($v, $sinonimosFinalizado, true)) {
                $v = 'FINALIZADO';
            }
            $this->merge(['estado_contrato' => $v]);
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
                ? ['bail', 'sometimes', 'required', 'string', 'max:150', Rule::in(config('rrhh.tipos_contrato'))]
                : ['bail', 'required', 'string', 'max:150', Rule::in(config('rrhh.tipos_contrato'))],

            'cod_empleado' => $ismethodPut
                ? 'bail|sometimes|required|exists:empleados,cod_empleado'
                : 'bail|required|exists:empleados,cod_empleado',

            'forma_de_pago' => $ismethodPut
                ? ['bail', 'sometimes', 'required', 'string', 'max:150', Rule::in(config('rrhh.formas_pago'))]
                : ['bail', 'required', 'string', 'max:150', Rule::in(config('rrhh.formas_pago'))],

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
                ? ['bail', 'sometimes', 'required', 'string', 'max:150', Rule::in(config('rrhh.modalidades_trabajo'))]
                : ['bail', 'required', 'string', 'max:150', Rule::in(config('rrhh.modalidades_trabajo'))],

            'horario_trabajo' => $ismethodPut
                ? ['bail', 'sometimes', 'required', 'string', 'max:150', Rule::in(config('rrhh.horarios_trabajo'))]
                : ['bail', 'required', 'string', 'max:150', Rule::in(config('rrhh.horarios_trabajo'))],

            'auxilio_transporte' => $ismethodPut
                ? 'bail|sometimes|required|boolean'
                : 'bail|required|boolean',

            'descripcion' => $ismethodPut
                ? 'bail|sometimes|nullable|string'
                : 'bail|nullable|string',

            'estado_contrato' => $ismethodPut
                ? 'bail|sometimes|required|string|in:ACTIVO,FINALIZADO'
                : 'bail|required|string|in:ACTIVO,FINALIZADO',
        ];
    }

    public function withValidator($validator): void
    {
        $validator->after(function ($validator) {
            $empleado = $this->resolverEmpleadoRelacionado();
            $estadoContrato = strtoupper((string) $this->input('estado_contrato', 'ACTIVO'));

            if ($empleado && $estadoContrato === 'ACTIVO' && strtoupper((string) $empleado->estado_emp) !== 'ACTIVO') {
                $validator->errors()->add(
                    'estado_contrato',
                    'No se puede activar un contrato si el empleado no está en estado ACTIVO.'
                );
            }

            if ($validator->errors()->hasAny(['cod_empleado', 'fecha_ingreso'])) {
                return;
            }

            $fechaIngresoRaw = $this->input('fecha_ingreso');
            if (! $fechaIngresoRaw || ! $empleado || ! $empleado->fecha_nac) {
                return;
            }

            $fechaIngreso = RrhhDates::parseFecha($fechaIngresoRaw);
            $fechaNacimiento = RrhhDates::parseFecha((string) $empleado->fecha_nac);

            if ($fechaIngreso->lt($fechaNacimiento)) {
                $validator->errors()->add(
                    'fecha_ingreso',
                    'La fecha de ingreso no puede ser anterior a la fecha de nacimiento.'
                );

                return;
            }

            $tipoDocumento = strtoupper((string) $empleado->tipo_documento);
            $edadMinima = RrhhDates::edadMinimaContrato($tipoDocumento);

            $fechaMinimaIngreso = $fechaNacimiento->copy()->addYears($edadMinima);
            if ($fechaIngreso->lt($fechaMinimaIngreso)) {
                $validator->errors()->add(
                    'fecha_ingreso',
                    "Para tipo de documento {$tipoDocumento}, la fecha de ingreso debe ser igual o posterior a cumplir {$edadMinima} años."
                );

                return;
            }

            $tipoContrato = (string) $this->input('tipo_contrato');
            if ($tipoContrato === '' && ($this->isMethod('put') || $this->isMethod('patch'))) {
                $codContrato = $this->route('contrato');
                $tipoContrato = (string) (Contrato::query()->find($codContrato)?->tipo_contrato ?? '');
            }

            $requiereFin = in_array($tipoContrato, config('rrhh.tipos_contrato_con_fecha_fin', []), true);
            if ($requiereFin && ! $this->filled('fecha_fin')) {
                $validator->errors()->add(
                    'fecha_fin',
                    'Los contratos a término fijo, obra o labor y aprendizaje requieren fecha de finalización.'
                );
            }
        });
    }

    private function resolverEmpleadoRelacionado(): ?Empleado
    {
        $codEmpleado = $this->input('cod_empleado');
        if ($codEmpleado) {
            return Empleado::query()->find($codEmpleado);
        }

        if (! $this->isMethod('put') && ! $this->isMethod('patch')) {
            return null;
        }

        $codContrato = $this->route('contrato');
        if (! $codContrato) {
            return null;
        }

        $contrato = Contrato::query()->find($codContrato);
        if (! $contrato) {
            return null;
        }

        return Empleado::query()->find($contrato->cod_empleado);
    }

    public function messages(): array
    {
        return [
            'tipo_contrato.required' => 'El tipo de contrato es obligatorio.',
            'tipo_contrato.in' => 'El tipo de contrato debe ser: '.implode(', ', config('rrhh.tipos_contrato')).'.',
            'cod_empleado.required' => 'El código del empleado es obligatorio.',
            'cod_empleado.exists' => 'El código del empleado no existe en la base de datos.',
            'forma_de_pago.required' => 'La forma de pago es obligatoria.',
            'forma_de_pago.in' => 'La forma de pago debe ser: '.implode(', ', config('rrhh.formas_pago')).'.',
            'fecha_ingreso.required' => 'La fecha de ingreso es obligatoria.',
            'fecha_ingreso.date' => 'La fecha de ingreso debe ser una fecha válida.',
            'fecha_ingreso.date_format' => 'La fecha de ingreso debe tener formato YYYY-MM-DD.',
            'fecha_fin.date' => 'La fecha de fin debe ser una fecha válida.',
            'fecha_fin.date_format' => 'La fecha de fin debe tener formato YYYY-MM-DD.',
            'fecha_fin.after_or_equal' => 'La fecha de fin debe ser igual o posterior a la fecha de ingreso (puede ser el mismo día).',
            'salario_base.required' => 'El salario base es obligatorio.',
            'salario_base.numeric' => 'El salario base debe ser un número.',
            'salario_base.min' => 'El salario base no puede ser negativo.',
            'cod_cargo.required' => 'El código del cargo es obligatorio.',
            'cod_cargo.exists' => 'El código del cargo no existe en la base de datos.',
            'modalidad_trabajo.required' => 'La modalidad de trabajo es obligatoria.',
            'modalidad_trabajo.in' => 'La modalidad debe ser: '.implode(', ', config('rrhh.modalidades_trabajo')).'.',
            'horario_trabajo.required' => 'El horario de trabajo es obligatorio.',
            'horario_trabajo.in' => 'El horario debe ser: '.implode(', ', config('rrhh.horarios_trabajo')).'.',
            'auxilio_transporte.required' => 'El auxilio de transporte es obligatorio.',
            'auxilio_transporte.boolean' => 'El auxilio de transporte debe ser un valor booleano.',
            'estado_contrato.required' => 'El estado del contrato es obligatorio.',
            'estado_contrato.in' => 'El estado del contrato debe ser ACTIVO o FINALIZADO.',
        ];
    }
}
