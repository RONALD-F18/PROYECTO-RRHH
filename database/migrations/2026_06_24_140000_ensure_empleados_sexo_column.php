<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasColumn('empleados', 'sexo')) {
            return;
        }

        Schema::table('empleados', function (Blueprint $table) {
            $table->string('sexo', 20)->default('Masculino')->after('fecha_nac');
        });
    }

    public function down(): void
    {
        if (! Schema::hasColumn('empleados', 'sexo')) {
            return;
        }

        Schema::table('empleados', function (Blueprint $table) {
            $table->dropColumn('sexo');
        });
    }
};
