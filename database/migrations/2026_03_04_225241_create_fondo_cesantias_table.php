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
        Schema::create('fondo_cesantias', function (Blueprint $table) {
            $table->id('cod_fondo_cesantia');
            $table->string('nombre_fondo_cesantia', 50)->unique();
            $table->string('descripcion_fondo_cesantia', 100);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropExists('fondo_cesantias');
}
};