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
        Schema::create('registros_ingreso', function (Blueprint $table) {
            $table->id();
            $table->foreignId('tarjeta_id')->constrained('tarjetas')->cascadeOnUpdate()->restrictOnDelete();
            $table->foreignId('vehiculo_id')->constrained('vehiculos')->cascadeOnUpdate()->restrictOnDelete();
            $table->foreignId('parqueo_id')->constrained('parqueos')->cascadeOnUpdate()->restrictOnDelete();
            $table->foreignId('espacio_id')->nullable()->constrained('espacios')->cascadeOnUpdate()->nullOnDelete();
            $table->dateTime('hora_ingreso');
            $table->dateTime('hora_salida')->nullable();          // se llena al salir
            $table->integer('tiempo_permanencia')->nullable();    // en minutos
            $table->enum('estado', ['activo', 'finalizado'])->default('activo');

            $table->timestamps();
            $table->softDeletes();

            $table->foreignId('created_by')->nullable()->constrained('usuarios')->nullOnDelete();
            $table->foreignId('updated_by')->nullable()->constrained('usuarios')->nullOnDelete();
            $table->foreignId('deleted_by')->nullable()->constrained('usuarios')->nullOnDelete();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('registros_ingreso');
    }
};
