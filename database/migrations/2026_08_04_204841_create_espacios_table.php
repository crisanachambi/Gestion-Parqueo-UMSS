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
        Schema::create('espacios', function (Blueprint $table) {
            $table->id();
            $table->foreignId('parqueo_id')
                  ->constrained('parqueos')->cascadeOnUpdate()->restrictOnDelete();
            $table->string('numero', 10);           // A-01, M-01...
            $table->enum('tipo', ['moto', 'auto'])->default('auto');
            $table->enum('estado', ['libre', 'ocupado', 'mantenimiento'])->default('libre');
 
            $table->timestamps();
            $table->softDeletes();
 
            $table->unsignedBigInteger('created_by')->nullable();
            $table->unsignedBigInteger('updated_by')->nullable();
            $table->unsignedBigInteger('deleted_by')->nullable();
 
            $table->tinyInteger('vigente')->storedAs('IF(deleted_at IS NULL, 1, NULL)');
 
            $table->unique(['parqueo_id', 'numero', 'vigente'], 'espacios_parqueo_numero_uq');
            // La app movil consulta disponibilidad con este indice.
            $table->index(['parqueo_id', 'tipo', 'estado'], 'espacios_disponibles_idx');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::disableForeignKeyConstraints();
        Schema::dropIfExists('espacios');
        Schema::enableForeignKeyConstraints();
    }
};
