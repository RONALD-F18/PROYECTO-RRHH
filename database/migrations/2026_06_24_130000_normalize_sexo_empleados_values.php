<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        DB::table('empleados')->where('sexo', 'MASCULINO')->update(['sexo' => 'Masculino']);
        DB::table('empleados')->where('sexo', 'FEMENINO')->update(['sexo' => 'Femenino']);
        DB::table('empleados')->where('sexo', 'OTRO')->update(['sexo' => 'Otro']);
    }

    public function down(): void
    {
        DB::table('empleados')->where('sexo', 'Masculino')->update(['sexo' => 'MASCULINO']);
        DB::table('empleados')->where('sexo', 'Femenino')->update(['sexo' => 'FEMENINO']);
        DB::table('empleados')->where('sexo', 'Otro')->update(['sexo' => 'OTRO']);
    }
};
