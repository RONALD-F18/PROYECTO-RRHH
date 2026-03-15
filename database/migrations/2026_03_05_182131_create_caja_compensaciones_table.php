<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('caja_compensaciones', function (Blueprint $table) {
            $table->id('cod_caja_compensacion'); // <-- clave primaria con el mismo nombre que la FK en afiliaciones
            $table->string('nombre_caja_compensacion', 50)->unique();
            $table->string('descripcion_caja_compensacion', 100);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('caja_compensaciones');
    }
};