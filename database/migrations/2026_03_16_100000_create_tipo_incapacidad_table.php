<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('tipo_incapacidad', function (Blueprint $table) {
            $table->id('cod_tipo_incapacidad');
            $table->string('nombre_tipo', 80);
            $table->string('descripcion', 200)->nullable();
            $table->string('clave_normativa', 30)->nullable()->comment('Clave para aplicar normativa de pago en Colombia: origen_comun, laboral, maternidad, paternidad');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('tipo_incapacidad');
    }
};
