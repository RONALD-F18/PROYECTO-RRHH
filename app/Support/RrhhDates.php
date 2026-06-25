<?php

namespace App\Support;

use Carbon\Carbon;
use Closure;

class RrhhDates
{
    public const ZONA_COLOMBIA = 'America/Bogota';

    public static function hoy(): Carbon
    {
        return Carbon::now(self::ZONA_COLOMBIA)->startOfDay();
    }

    public static function haceAnios(int $anios): string
    {
        return self::hoy()->copy()->subYears($anios)->format('Y-m-d');
    }

    public static function parseFecha(string $fecha): Carbon
    {
        return Carbon::parse($fecha, self::ZONA_COLOMBIA)->startOfDay();
    }

    /**
     * Valida fecha de nacimiento según tipo de documento (registro empleado).
     */
    public static function validarFechaNacEmpleado(string $tipoDocumento, string $fechaNac, Closure $fail): void
    {
        $tipo = strtoupper(trim($tipoDocumento));
        if ($tipo === '') {
            return;
        }

        try {
            $nac = self::parseFecha($fechaNac);
        } catch (\Throwable) {
            $fail('La fecha de nacimiento no es válida.');

            return;
        }

        $hoy = self::hoy();
        $edadMinima = max(15, (int) config('rrhh.empleado_edad_minima', 15));

        if ($nac->gte($hoy)) {
            $fail('La fecha de nacimiento no puede ser hoy ni una fecha futura.');

            return;
        }

        if ($nac->lt($hoy->copy()->subYears(120))) {
            $fail('La fecha de nacimiento no es válida: verifique edad máxima (120 años).');

            return;
        }

        $limiteMinimoLaboral = $hoy->copy()->subYears($edadMinima);
        $limiteMayoriaEdad = $hoy->copy()->subYears(18);
        $limiteTiMinima = $hoy->copy()->subYears(7);

        match ($tipo) {
            'CC' => self::validarCc($nac, $limiteMayoriaEdad, $fail),
            'TI' => self::validarTi($nac, $limiteMayoriaEdad, $limiteTiMinima, $limiteMinimoLaboral, $edadMinima, $fail),
            default => self::validarEdadMinimaLaboral($nac, $limiteMinimoLaboral, $edadMinima, $fail),
        };
    }

    private static function validarCc(Carbon $nac, Carbon $limiteMayoriaEdad, Closure $fail): void
    {
        if ($nac->gt($limiteMayoriaEdad)) {
            $fail('Con cédula de ciudadanía (CC), el empleado debe ser mayor de edad (18 años o más).');
        }
    }

    private static function validarTi(
        Carbon $nac,
        Carbon $limiteMayoriaEdad,
        Carbon $limiteTiMinima,
        Carbon $limiteMinimoLaboral,
        int $edadMinima,
        Closure $fail
    ): void {
        if ($nac->lte($limiteMayoriaEdad)) {
            $fail('Con tarjeta de identidad (TI), el empleado debe ser menor de 18 años.');

            return;
        }

        if ($nac->gt($limiteTiMinima)) {
            $fail('Con tarjeta de identidad (TI), el empleado debe tener al menos 7 años.');

            return;
        }

        self::validarEdadMinimaLaboral($nac, $limiteMinimoLaboral, $edadMinima, $fail);
    }

    private static function validarEdadMinimaLaboral(
        Carbon $nac,
        Carbon $limiteMinimoLaboral,
        int $edadMinima,
        Closure $fail
    ): void {
        if ($nac->gt($limiteMinimoLaboral)) {
            $fail("El empleado debe tener al menos {$edadMinima} años para vínculo laboral.");
        }
    }

    /**
     * Fecha de ingreso mínima según tipo de documento del empleado.
     */
    public static function edadMinimaContrato(string $tipoDocumento): int
    {
        return match (strtoupper(trim($tipoDocumento))) {
            'TI' => max(15, (int) config('rrhh.empleado_edad_minima', 15)),
            'CC', 'CE', 'PASAPORTE' => 18,
            default => max(15, (int) config('rrhh.empleado_edad_minima', 15)),
        };
    }
}
