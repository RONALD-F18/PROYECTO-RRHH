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
        Schema::create('empleados', function (Blueprint $table) {
            $table->id('cod_empleado');                           // ID autoincremental
            $table->string('nombre_empleado', 100);
            $table->string('apellidos_empleado', 100);
            $table->string('doc_iden', 50)->unique();             // Documento de identidad único
            $table->string('tipo_documento', 50);
            $table->date('fecha_nac');
            $table->string('direccion', 200);
            $table->string('numero_telefono', 50);
            $table->string('numero_cuenta', 50)->unique();
            $table->string('tipo_cuenta', 50);
            $table->foreignId('cod_banco')->constrained('bancos', 'cod_banco')->onDelete('cascade'); // Relación con banco
            $table->string('estado_emp', 20);
            $table->string('discapacidad', 50);
            $table->string('nacionalidad', 50);
            $table->string('estado_civil', 50);
            $table->string('grupo_sanguineo', 10);
            $table->string('profesion', 100);
            $table->date('fec_exp_doc');                           // Fecha de expedición del documento
            $table->text('descripcion');                           // Campo requerido, no nulo
            $table->foreignId('cod_usuario')->constrained('usuarios', 'cod_usuario')->onDelete('cascade'); // Relación usuario
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('empleados');
    }
};