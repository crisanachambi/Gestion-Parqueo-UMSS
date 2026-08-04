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
        Schema::create('pagos', function (Blueprint $table) {
            $table->id();
            $table->foreignId('registro_id')->constrained('registros_ingreso')->cascadeOnUpdate()->restrictOnDelete();
            $table->foreignId('tarjeta_id')->constrained('tarjetas')->cascadeOnUpdate()->restrictOnDelete();
            $table->foreignId('parqueo_id')->constrained('parqueos')->cascadeOnUpdate()->restrictOnDelete();
            $table->decimal('monto', 8, 2);
            $table->decimal('saldo_anterior', 8, 2);
            $table->decimal('saldo_posterior', 8, 2);
            $table->enum('metodo', ['tarjeta', 'efectivo'])->default('tarjeta');
            $table->enum('estado', ['exitoso', 'anulado'])->default('exitoso');
            $table->dateTime('fecha_pago');

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
        Schema::dropIfExists('pagos');
    }
};
