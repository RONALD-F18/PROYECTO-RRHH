<?php

namespace App\Support;

class RrhhCatalog
{
    /**
     * Normaliza un valor enviado por el cliente al valor canónico definido en config.
     */
    public static function normalizar(string $valor, array $opciones): ?string
    {
        $valor = trim($valor);
        if ($valor === '') {
            return null;
        }

        foreach ($opciones as $opcion) {
            if (strcasecmp($valor, (string) $opcion) === 0) {
                return (string) $opcion;
            }
        }

        return null;
    }

    public static function mapaMinusculas(array $opciones): array
    {
        $mapa = [];
        foreach ($opciones as $opcion) {
            $mapa[mb_strtolower((string) $opcion)] = (string) $opcion;
        }

        return $mapa;
    }

    public static function normalizarDesdeMapa(string $valor, array $mapa): ?string
    {
        $clave = mb_strtolower(trim($valor));

        return $mapa[$clave] ?? null;
    }
}
