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
        Schema::create('personas', function (Blueprint $table) {
            $table->uuid('id')->primary();

            $table->string('email')->unique();
            $table->string('telefono', 15)->nullable();
            $table->string('comprobante_residencia')->nullable();

            $table->string('codigo_talento')->unique();
            $table->text('resumen')->nullable();

            $table->string('nivel_educacional')->nullable();
            $table->string('titulo_carrera')->nullable();
            $table->integer('anio_egreso')->nullable();

            $table->integer('anios_experiencia')->default(0);
            $table->json('areas_experiencia')->nullable();

            $table->json('competencias')->nullable();
            $table->string('rango_renta')->nullable();
            $table->string('tipo_jornada')->nullable();
            $table->string('modalidad')->nullable();

            $table->json('cursos')->nullable();
            $table->json('idiomas')->nullable();

            $table->text('portafolio_url')->nullable();
            $table->boolean('persona_discapacidad')->default(false);

            $table->boolean('validado')->default(false);
            $table->boolean('activo')->default(true);
            $table->integer('porcentaje_completitud')->default(0);

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('personas');
    }
};
