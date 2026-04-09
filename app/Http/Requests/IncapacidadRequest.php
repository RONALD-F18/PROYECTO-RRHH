<?php

namespace App\Http\Requests;

use App\Models\Contrato;
use App\Models\Empleado;
use App\Models\Incapacidad;
use Carbon\Carbon;
use Illuminate\Foundation\Http\FormRequest;

class IncapacidadRequest extends FormRequest
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

        $id = $this->resolverIdIncapacidadEnRuta();
        if ($id === null || $id === '') {
            return;
        }

        $incapacidad = Incapacidad::query()->find($id);
        if (! $incapacidad) {
            return;
        }

        $merge = [];
        foreach (['fecha_inicio', 'fecha_fin', 'cod_empleado', 'cod_tipo_incapacidad'] as $campo) {
            if (! $this->has($campo) || $this->input($campo) === null || $this->input($campo) === '') {
                $valor = $incapacidad->{$campo};
                $merge[$campo] = $valor instanceof \DateTimeInterface
                    ? $valor->format('Y-m-d')
                    : $valor;
            }
        }

        if (! $this->has('fecha_radicacion') || $this->input('fecha_radicacion') === null || $this->input('fecha_radicacion') === '') {
            $rad = $incapacidad->fecha_radicacion;
            if ($rad !== null) {
                $merge['fecha_radicacion'] = $rad instanceof \DateTimeInterface
                    ? $rad->format('Y-m-d')
                    : $rad;
            }
        }

        if ($merge !== []) {
            $this->merge($merge);
        }
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

    public function withValidator($validator): void
    {
        $validator->after(function ($validator): void {
            if ($validator->errors()->has('cod_empleado')) {
                return;
            }

            $codEmpleado = (int) $this->input('cod_empleado');
            $empleado = Empleado::query()->find($codEmpleado);
            if (! $empleado || ! $empleado->fecha_nac) {
                return;
            }

            $fechaNacimiento = Carbon::parse($empleado->fecha_nac)->startOfDay();
            $edadMinima = max(15, (int) config('rrhh.empleado_edad_minima', 15));
            $fechaMinimaLaboral = $fechaNacimiento->copy()->addYears($edadMinima);

            $fechaRefContrato = $this->fechaIngresoReferenciaContratos($codEmpleado);

            foreach (['fecha_inicio', 'fecha_fin', 'fecha_radicacion'] as $campo) {
                if ($validator->errors()->has($campo)) {
                    continue;
                }
                if ($campo === 'fecha_radicacion' && ! $this->filled('fecha_radicacion')) {
                    continue;
                }
                if (! $this->filled($campo)) {
                    continue;
                }

                try {
                    $fecha = Carbon::parse($this->input($campo))->startOfDay();
                } catch (\Throwable) {
                    continue;
                }

                $this->validarFechaVsNacimientoYEdad(
                    $validator,
                    $campo,
                    $fecha,
                    $fechaNacimiento,
                    $fechaMinimaLaboral,
                    $edadMinima
                );

                if ($validator->errors()->has($campo)) {
                    continue;
                }

                if ($fechaRefContrato !== null && $fecha->lt($fechaRefContrato)) {
                    $validator->errors()->add(
                        $campo,
                        $this->mensajeContratoReferencia($campo)
                    );
                }
            }

            if ($fechaRefContrato === null
                && ($this->filled('fecha_inicio') || $this->filled('fecha_fin') || $this->filled('fecha_radicacion'))) {
                foreach (['fecha_inicio', 'fecha_fin'] as $campo) {
                    if (! $this->filled($campo) || $validator->errors()->has($campo)) {
                        continue;
                    }
                    $validator->errors()->add(
                        $campo,
                        'El empleado no tiene contratos con fecha de ingreso. Registre un contrato antes de registrar incapacidades.'
                    );
                }
                if ($this->filled('fecha_radicacion') && ! $validator->errors()->has('fecha_radicacion')) {
                    $validator->errors()->add(
                        'fecha_radicacion',
                        'El empleado no tiene contratos con fecha de ingreso. Registre un contrato antes de registrar incapacidades.'
                    );
                }
            }

            if ($validator->errors()->has('fecha_inicio') || $validator->errors()->has('fecha_fin')) {
                return;
            }

            if (! $this->filled('fecha_inicio') || ! $this->filled('fecha_fin')) {
                return;
            }

            if ($this->filled('fecha_radicacion') && ! $validator->errors()->has('fecha_radicacion')) {
                try {
                    $inicio = Carbon::parse($this->input('fecha_inicio'))->startOfDay();
                    $rad = Carbon::parse($this->input('fecha_radicacion'))->startOfDay();
                    if ($rad->lt($inicio)) {
                        $validator->errors()->add(
                            'fecha_radicacion',
                            'La fecha de radicación debe ser igual o posterior a la fecha de inicio de la incapacidad.'
                        );
                    }
                } catch (\Throwable) {
                    // omitir
                }
            }
        });
    }

    private function validarFechaVsNacimientoYEdad(
        $validator,
        string $campo,
        Carbon $fecha,
        Carbon $fechaNacimiento,
        Carbon $fechaMinimaLaboral,
        int $edadMinima
    ): void {
        if ($fecha->lt($fechaNacimiento)) {
            $validator->errors()->add(
                $campo,
                $this->mensajeAnteriorNacimiento($campo)
            );

            return;
        }

        if ($fecha->lt($fechaMinimaLaboral)) {
            $validator->errors()->add(
                $campo,
                $this->mensajeAntesEdadMinima($campo, $edadMinima)
            );
        }
    }

    private function mensajeAnteriorNacimiento(string $campo): string
    {
        return match ($campo) {
            'fecha_inicio' => 'La fecha de inicio no puede ser anterior a la fecha de nacimiento del empleado.',
            'fecha_fin' => 'La fecha de fin no puede ser anterior a la fecha de nacimiento del empleado.',
            'fecha_radicacion' => 'La fecha de radicación no puede ser anterior a la fecha de nacimiento del empleado.',
            default => 'La fecha no puede ser anterior a la fecha de nacimiento del empleado.',
        };
    }

    private function mensajeAntesEdadMinima(string $campo, int $edadMinima): string
    {
        return match ($campo) {
            'fecha_inicio' => "La fecha de inicio debe ser igual o posterior a que el empleado cumpla {$edadMinima} años.",
            'fecha_fin' => "La fecha de fin debe ser igual o posterior a que el empleado cumpla {$edadMinima} años.",
            'fecha_radicacion' => "La fecha de radicación debe ser igual o posterior a que el empleado cumpla {$edadMinima} años.",
            default => "La fecha debe ser igual o posterior a que el empleado cumpla {$edadMinima} años.",
        };
    }

    private function mensajeContratoReferencia(string $campo): string
    {
        return match ($campo) {
            'fecha_inicio' => 'La fecha de inicio no puede ser anterior a la fecha de ingreso del contrato de referencia.',
            'fecha_fin' => 'La fecha de fin no puede ser anterior a la fecha de ingreso del contrato de referencia.',
            'fecha_radicacion' => 'La fecha de radicación no puede ser anterior a la fecha de ingreso del contrato de referencia.',
            default => 'La fecha no puede ser anterior a la fecha de ingreso del contrato de referencia.',
        };
    }

    /**
     * Fecha de ingreso más temprana entre contratos ACTIVO; si no hay, la más temprana entre todos con fecha_ingreso.
     */
    private function fechaIngresoReferenciaContratos(int $codEmpleado): ?Carbon
    {
        $minActivo = Contrato::query()
            ->where('cod_empleado', $codEmpleado)
            ->where('estado_contrato', 'ACTIVO')
            ->whereNotNull('fecha_ingreso')
            ->min('fecha_ingreso');

        if ($minActivo !== null) {
            return Carbon::parse($minActivo)->startOfDay();
        }

        $minTodos = Contrato::query()
            ->where('cod_empleado', $codEmpleado)
            ->whereNotNull('fecha_ingreso')
            ->min('fecha_ingreso');

        return $minTodos !== null ? Carbon::parse($minTodos)->startOfDay() : null;
    }

    private function resolverIdIncapacidadEnRuta(): mixed
    {
        $route = $this->route();
        if (! $route) {
            return null;
        }

        $params = $route->parameters();

        return $params['incapacidad']
            ?? $params['incapacidade']
            ?? (count($params) === 1 ? reset($params) : null);
    }

    public function messages(): array
    {
        return [
            'fecha_inicio.required' => 'La fecha de inicio es obligatoria.',
            'fecha_fin.required' => 'La fecha de fin es obligatoria.',
            'fecha_fin.after_or_equal' => 'La fecha de fin debe ser igual o posterior a la fecha de inicio.',
            'fecha_radicacion.date' => 'La fecha de radicación debe ser una fecha válida.',
            'cod_tipo_incapacidad.required' => 'El tipo de incapacidad es obligatorio.',
            'cod_tipo_incapacidad.exists' => 'El tipo de incapacidad no existe.',
            'cod_empleado.required' => 'El empleado es obligatorio.',
            'cod_empleado.exists' => 'El empleado no existe.',
            'cod_clasificacion_enfermedad.exists' => 'La clasificación de enfermedad no existe.',
        ];
    }
}
