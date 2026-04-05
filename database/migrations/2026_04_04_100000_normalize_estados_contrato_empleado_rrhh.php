<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

/**
 * Alineación con front React: contrato ACTIVO|FINALIZADO; empleado ACTIVO|RETIRADO.
 * Datos legado: Vigente→ACTIVO; demás no finales→FINALIZADO. INACTIVO empleado→RETIRADO.
 */
return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasTable('contrato')) {
            DB::table('contrato')
                ->whereRaw("UPPER(TRIM(estado_contrato)) IN ('VIGENTE', 'VIGENCIA')")
                ->update(['estado_contrato' => 'ACTIVO']);

            DB::table('contrato')
                ->whereNotIn('estado_contrato', ['ACTIVO', 'FINALIZADO'])
                ->update(['estado_contrato' => 'FINALIZADO']);
        }

        if (Schema::hasTable('empleados')) {
            DB::table('empleados')->where('estado_emp', 'INACTIVO')
                ->update(['estado_emp' => 'RETIRADO']);

            DB::table('empleados')
                ->whereNotIn('estado_emp', ['ACTIVO', 'RETIRADO'])
                ->update(['estado_emp' => 'RETIRADO']);
        }
    }

    public function down(): void
    {
        // Irreversible sin backup de valores originales.
    }
};
