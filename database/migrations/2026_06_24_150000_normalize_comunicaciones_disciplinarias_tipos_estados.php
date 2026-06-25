<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

/**
 * Tipos: LLAMADO_VERBAL | MEMORANDO | FELICITACION.
 * Estados: EMITIDO | NOTIFICADO.
 */
return new class extends Migration
{
    private function mapTipo(string $valor): string
    {
        $s = mb_strtolower(trim($valor));

        if (str_contains($s, 'felicit')) {
            return 'FELICITACION';
        }
        if (str_contains($s, 'llamado') && str_contains($s, 'verbal')) {
            return 'LLAMADO_VERBAL';
        }
        if (str_contains($s, 'compromiso') && str_contains($s, 'mejora')) {
            return 'LLAMADO_VERBAL';
        }
        if (str_contains($s, 'suspens')) {
            return 'MEMORANDO';
        }
        if (str_contains($s, 'apercibimiento')) {
            return 'MEMORANDO';
        }
        if (str_contains($s, 'memorand')) {
            return 'MEMORANDO';
        }

        $upper = strtoupper(str_replace(' ', '_', $s));
        if (in_array($upper, ['LLAMADO_VERBAL', 'MEMORANDO', 'FELICITACION'], true)) {
            return $upper;
        }

        return 'MEMORANDO';
    }

    private function mapEstado(string $valor): string
    {
        $s = mb_strtolower(trim($valor));

        if (str_contains($s, 'notific') || str_contains($s, 'cerrad')) {
            return 'NOTIFICADO';
        }
        if (str_contains($s, 'emit') || str_contains($s, 'seguimiento') || str_contains($s, 'borrador') || str_contains($s, 'activa')) {
            return 'EMITIDO';
        }

        $upper = strtoupper($s);
        if (in_array($upper, ['EMITIDO', 'NOTIFICADO'], true)) {
            return $upper;
        }

        return 'EMITIDO';
    }

    public function up(): void
    {
        if (! Schema::hasTable('comunicaciones_disciplinarias')) {
            return;
        }

        $filas = DB::table('comunicaciones_disciplinarias')->select(
            'cod_disciplinario',
            'tipo_comunicacion',
            'estado_comunicacion'
        )->get();

        foreach ($filas as $fila) {
            DB::table('comunicaciones_disciplinarias')
                ->where('cod_disciplinario', $fila->cod_disciplinario)
                ->update([
                    'tipo_comunicacion' => $this->mapTipo((string) $fila->tipo_comunicacion),
                    'estado_comunicacion' => $this->mapEstado((string) $fila->estado_comunicacion),
                    'updated_at' => now(),
                ]);
        }
    }

    public function down(): void
    {
        // Irreversible sin respaldo de valores originales.
    }
};
