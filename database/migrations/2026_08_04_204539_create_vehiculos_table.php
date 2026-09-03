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
        // COMPARTIDA entre los 3 parqueos: no lleva parqueo_id
        // ni Global Scope. La persona y su vehiculo son unicos
        // en la universidad.
        Schema::create('vehiculos', function (Blueprint $table) {
            $table->id();
            $table->foreignId('usuario_id')
                  ->constrained('usuarios')->cascadeOnUpdate()->restrictOnDelete();
            $table->string('placa', 15);
            $table->enum('tipo', ['moto', 'auto']);
            $table->string('marca', 60)->nullable();
            $table->string('color', 40)->nullable();
            $table->tinyInteger('activo')->default(1);
 
            $table->timestamps();
            $table->softDeletes();
 
            $table->unsignedBigInteger('created_by')->nullable();
            $table->unsignedBigInteger('updated_by')->nullable();
            $table->unsignedBigInteger('deleted_by')->nullable();
 
            $table->tinyInteger('vigente')->storedAs('IF(deleted_at IS NULL, 1, NULL)');
 
            // La placa se libera si el vehiculo se da de baja.
            $table->unique(['placa', 'vigente'], 'vehiculos_placa_uq');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::disableForeignKeyConstraints();
        Schema::dropIfExists('vehiculos');
        Schema::enableForeignKeyConstraints();
    }
};
