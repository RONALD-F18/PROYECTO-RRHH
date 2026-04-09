<?php

namespace App\Http\Requests;

use App\Models\Afiliacion;
use App\Models\Empleado;
use Carbon\Carbon;
use Illuminate\Foundation\Http\FormRequest;

class AfiliacionRequest extends FormRequest
{
    /** @var list<string> */
    private const FECHAS_AFILIACION = [
        'fecha_afiliacion_eps',
        'fecha_afiliacion_arl',
        'fecha_afiliacion_fondo_pensiones',
        'fecha_afiliacion_fondo_cesantias',
        'fecha_afiliacion_caja',
    ];

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
       $isMethodPut = $this->isMethod('put') || $this->isMethod('patch');

        return [
            'fecha_afiliacion_eps' => $isMethodPut
            ? 'sometimes|required|date'
            : 'required|date',
            'fecha_afiliacion_arl' => $isMethodPut
            ? 'sometimes|required|date'
            : 'required|date',
            'fecha_afiliacion_caja' => $isMethodPut
            ? 'sometimes|required|date'
            : 'required|date',
            'fecha_afiliacion_fondo_pensiones' => $isMethodPut
            ? 'sometimes|required|date'
            : 'required|date',
            'fecha_afiliacion_fondo_cesantias' => $isMethodPut
            ? 'sometimes|required|date'
            : 'required|date',
            'estado_afiliacion' => $isMethodPut
            ? 'sometimes|required|string|max:20'
            : 'required|string|max:20',
            'cod_eps' => $isMethodPut
            ? 'sometimes|required|integer|exists:eps,cod_eps'
            : 'required|integer|exists:eps,cod_eps',
            'cod_riesgo' => $isMethodPut
            ? 'sometimes|required|integer|exists:riesgos,cod_riesgo'
            : 'required|integer|exists:riesgos,cod_riesgo',
            'cod_arl' => $isMethodPut
            ? 'sometimes|required|integer|exists:arls,cod_arl'
            : 'required|integer|exists:arls,cod_arl',
            'cod_fondo_pensiones' => $isMethodPut
            ? 'sometimes|required|integer|exists:fondo_pensiones,cod_fondo_pensiones'
            : 'required|integer|exists:fondo_pensiones,cod_fondo_pensiones',
            'cod_fondo_cesantias' => $isMethodPut
            ? 'sometimes|required|integer|exists:fondo_cesantias,cod_fondo_cesantias'
            : 'required|integer|exists:fondo_cesantias,cod_fondo_cesantias',
            'cod_caja_compensacion' => $isMethodPut
            ? 'sometimes|required|integer|exists:caja_compensaciones,cod_caja_compensacion'
            : 'required|integer|exists:caja_compensaciones,cod_caja_compensacion',
            'cod_empleado' => $isMethodPut
            ? 'sometimes|required|integer|exists:empleados,cod_empleado'
            : 'required|integer|exists:empleados,cod_empleado',
            'descripcion' => $isMethodPut
            ? 'sometimes|required|string|max:200'
            : 'required|string|max:200',
            'tipo_regimen' => $isMethodPut
            ? 'sometimes|required|string|max:12'
            : 'required|string|max:12',
];
    }

    public function withValidator($validator): void
    {
        $validator->after(function ($validator): void {
            if ($validator->errors()->has('cod_empleado')) {
                return;
            }

            $empleado = $this->resolverEmpleadoPersistido();
            if (! $empleado || ! $empleado->fecha_nac) {
                return;
            }

            $edadMinima = max(15, (int) config('rrhh.empleado_edad_minima', 15));
            $fechaNacimiento = Carbon::parse($empleado->fecha_nac)->startOfDay();
            $fechaMinimaLaboral = $fechaNacimiento->copy()->addYears($edadMinima);

            foreach (self::FECHAS_AFILIACION as $campo) {
                if ($validator->errors()->has($campo)) {
                    continue;
                }
                if (! $this->filled($campo)) {
                    continue;
                }

                try {
                    $fechaAfiliacion = Carbon::parse($this->input($campo))->startOfDay();
                } catch (\Throwable) {
                    continue;
                }

                $etiqueta = $this->etiquetaCampoFechaAfiliacion($campo);

                if ($fechaAfiliacion->lt($fechaNacimiento)) {
                    $validator->errors()->add(
                        $campo,
                        "{$etiqueta} no puede ser anterior a la fecha de nacimiento."
                    );
                    continue;
                }

                if ($fechaAfiliacion->lt($fechaMinimaLaboral)) {
                    $validator->errors()->add(
                        $campo,
                        "{$etiqueta} debe ser igual o posterior a cumplir {$edadMinima} años."
                    );
                }
            }
        });
    }

    private function resolverEmpleadoPersistido(): ?Empleado
    {
        $codEmpleado = $this->input('cod_empleado');
        if ($codEmpleado !== null && $codEmpleado !== '') {
            return Empleado::query()->find((int) $codEmpleado);
        }

        if (! $this->isMethod('put') && ! $this->isMethod('patch')) {
            return null;
        }

        $idAfiliacion = $this->resolverIdAfiliacionDesdeRuta();
        if ($idAfiliacion === null || $idAfiliacion === '') {
            return null;
        }

        $afiliacion = Afiliacion::query()->find($idAfiliacion);

        return $afiliacion
            ? Empleado::query()->find((int) $afiliacion->cod_empleado)
            : null;
    }

    private function resolverIdAfiliacionDesdeRuta(): mixed
    {
        $route = $this->route();
        if (! $route) {
            return null;
        }

        $params = $route->parameters();

        return $params['afiliacion']
            ?? $params['afiliacione']
            ?? (count($params) === 1 ? reset($params) : null);
    }

    private function etiquetaCampoFechaAfiliacion(string $campo): string
    {
        return match ($campo) {
            'fecha_afiliacion_eps' => 'La fecha de afiliación a la EPS',
            'fecha_afiliacion_arl' => 'La fecha de afiliación a la ARL',
            'fecha_afiliacion_fondo_pensiones' => 'La fecha de afiliación al fondo de pensiones',
            'fecha_afiliacion_fondo_cesantias' => 'La fecha de afiliación al fondo de cesantías',
            'fecha_afiliacion_caja' => 'La fecha de afiliación a la caja de compensación',
            default => 'La fecha de afiliación',
        };
    }

    public function messages(): array
    {
        return[
            'fecha_afiliacion_eps.required' => 'La fecha de afiliación a la EPS es obligatoria.',
            'fecha_afiliacion_eps.date' => 'La fecha de afiliación a la EPS debe ser una fecha válida.',
            'fecha_afiliacion_arl.required' => 'La fecha de afiliación a la ARL es obligatoria.',
            'fecha_afiliacion_arl.date' => 'La fecha de afiliación a la ARL debe ser una fecha válida.',
            'fecha_afiliacion_caja.required' => 'La fecha de afiliación a la caja es obligatoria.',
            'fecha_afiliacion_caja.date' => 'La fecha de afiliación a la caja debe ser una fecha válida.',
            'fecha_afiliacion_fondo_pensiones.required' => 'La fecha de afiliación a los fondos de pensiones es obligatoria.',
            'fecha_afiliacion_fondo_pensiones.date' => 'La fecha de afiliación a los fondos de pensiones debe ser una fecha válida.',
            'fecha_afiliacion_fondo_cesantias.required' => 'La fecha de afiliación a los fondos de cesantías es obligatoria.',
            'fecha_afiliacion_fondo_cesantias.date' => 'La fecha de afiliación a los fondos de cesantías debe ser una fecha válida.',
            'estado_afiliacion.required' => 'El estado de la afiliación es obligatorio.',
            'estado_afiliacion.string' => 'El estado de la afiliación debe ser una cadena de texto.',
            'estado_afiliacion.max' => 'El estado de la afiliación no debe exceder los 20 caracteres.',
            'cod_eps.required' => 'El código de la EPS es obligatorio.',
            'cod_eps.integer' => 'El código de la EPS debe ser un número entero.',
            'cod_eps.exists' => 'El código de la EPS no existe.',
            'cod_riesgo.required' => 'El código de la riesgo es obligatorio.',
            'cod_riesgo.integer' => 'El código de la riesgo debe ser un número entero.',
            'cod_riesgo.exists' => 'El código de la riesgo no existe.',
            'cod_arl.required' => 'El código de la ARL es obligatorio.',
            'cod_arl.integer' => 'El código de la ARL debe ser un número entero.',
            'cod_arl.exists' => 'El código de la ARL no existe.',
            'cod_fondo_pensiones.required' => 'El código de los fondos de pensiones es obligatorio.',
            'cod_fondo_pensiones.integer' => 'El código de los fondos de pensiones debe ser un número entero.',
            'cod_fondo_pensiones.exists' => 'El código de los fondos de pensiones no existe.',
            'cod_fondo_cesantias.required' => 'El código de los fondos de cesantías es obligatorio.',
            'cod_fondo_cesantias.integer' => 'El código de los fondos de cesantías debe ser un número entero.',
            'cod_fondo_cesantias.exists' => 'El código de los fondos de cesantías no existe.',
            'cod_caja_compensacion.required' => 'El código de la caja de compensación es obligatorio.',
            'cod_caja_compensacion.integer' => 'El código de la caja de compensación debe ser un número entero.',
            'cod_caja_compensacion.exists' => 'El código de la caja de compensación no existe.',
            'cod_empleado.required' => 'El código del empleado es obligatorio.',
            'cod_empleado.integer' => 'El código del empleado debe ser un número entero.',
            'cod_empleado.exists' => 'El código del empleado no existe.',
            'descripcion.required' => 'La descripción es obligatoria.',
            'descripcion.string' => 'La descripción debe ser una cadena de texto.',
            'descripcion.max' => 'La descripción no debe exceder los 200 caracteres.',
            'tipo_regimen.required' => 'El tipo de régimen es obligatorio.',
            'tipo_regimen.string' => 'El tipo de régimen debe ser una cadena de texto.',
            'tipo_regimen.max' => 'El tipo de régimen no debe exceder los 12 caracteres.',
        ];
    }
}