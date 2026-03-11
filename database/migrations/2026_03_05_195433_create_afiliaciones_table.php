<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::create('afiliaciones', function (Blueprint $table) {
            $table->id('cod_afiliacion'); // PK

            // Fechas de afiliación opcionales
            $table->date('fecha_afiliacion_eps');
            $table->date('fecha_afiliacion_arl');
            $table->date('fecha_afiliacion_caja');
            $table->date('fecha_afiliacion_fondo_pensiones');
            $table->date('fecha_afiliacion_fondo_cesantias');
            $table->string('estado_afiliacion', 20);

            // Relación con EPS
            $table->foreignId('cod_eps')->constrained('eps', 'cod_eps')->onDelete('cascade');
        
            // Relación con Riesgos
            $table->foreignId('cod_riesgo')->constrained('riesgos', 'cod_riesgo')->onDelete('cascade');
            // Relación con ARL
            $table->foreignId('cod_arl')->constrained('arls', 'cod_arl')->onDelete('cascade');
            // Relación con Fondo de Pensiones
            $table->foreignId('cod_fondo_pensiones')->constrained('fondo_pensiones', 'cod_fondo_pensiones')->onDelete('cascade');
            $table->foreignId('cod_fondo_cesantias')->constrained('fondo_cesantias', 'cod_fondo_cesantias')->onDelete('cascade');

            // **Única relación activa con la caja de compensación**
            $table->foreignId('cod_caja_compensacion')->constrained('caja_compensaciones', 'cod_caja_compensacion')->onDelete('cascade');

            //Relacion con empleado
            $table->foreignId('cod_empleado')->constrained('empleados', 'cod_empleado')->onDelete('cascade');

            // Información adicional
            $table->string('descripcion', 200)->nullable();
            $table->string('tipo_regimen', 12);

            $table->timestamps();
        });
    }

    public function down(): void {
        Schema::dropIfExists('afiliaciones');
    }
};