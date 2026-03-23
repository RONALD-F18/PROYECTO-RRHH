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
        Schema::create('empresas', function (Blueprint $table) {
            $table->id('id_empresa');

            $table->string('nit', 20)->unique();
            $table->string('dv', 2);

            $table->string('razon_social', 150);
            $table->string('nombre_comercial', 150)->nullable();

            $table->string('tipo_empresa', 20)->nullable();
            $table->string('estado_empresa', 20)->nullable();
            $table->date('fecha_constitucion')->nullable();

            $table->string('direccion', 200)->nullable();
            $table->string('ciudad', 100)->nullable();
            $table->string('departamento', 100)->nullable();
            $table->string('pais', 50)->default('Colombia');

            $table->string('telefono', 20)->nullable();
            $table->string('correo', 100)->nullable();
            $table->string('pagina_web', 100)->nullable();

            $table->string('nombre_representante', 150)->nullable();
            $table->string('documento_representante', 20)->nullable();

            $table->timestamp('fecha_creacion')->useCurrent();
            $table->timestamp('fecha_actualizacion')->nullable();

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('empresas');
    }
};

