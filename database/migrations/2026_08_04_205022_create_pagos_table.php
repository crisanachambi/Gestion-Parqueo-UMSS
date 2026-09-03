<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Entidad de transaccion exigida por el tutor:
        // monto, fecha, metodo y estado de cada cobro.
        Schema::create('pagos', function (Blueprint $table) {
            $table->id();
            $table->foreignId('registro_id')
                  ->constrained('registros_ingreso')->cascadeOnUpdate()->restrictOnDelete();
            // NULLABLE: un pago en efectivo no tiene tarjeta.
            $table->foreignId('tarjeta_id')->nullable()
                  ->constrained('tarjetas')->cascadeOnUpdate()->restrictOnDelete();
            $table->foreignId('parqueo_id')
                  ->constrained('parqueos')->cascadeOnUpdate()->restrictOnDelete();
 
            $table->decimal('monto', 8, 2);
            // NULLABLE: solo aplican cuando el metodo es 'tarjeta'.
            $table->decimal('saldo_anterior', 8, 2)->nullable();
            $table->decimal('saldo_posterior', 8, 2)->nullable();
            $table->enum('metodo', ['tarjeta', 'efectivo'])->default('tarjeta');
            $table->enum('estado', ['exitoso', 'anulado'])->default('exitoso');
            $table->dateTime('fecha_pago');
 
            $table->timestamps();
            $table->softDeletes();
 
            $table->unsignedBigInteger('created_by')->nullable();
            $table->unsignedBigInteger('updated_by')->nullable();
            $table->unsignedBigInteger('deleted_by')->nullable();
 
            $table->index(['parqueo_id', 'fecha_pago'], 'pagos_parqueo_fecha_idx');
        });
 
        DB::statement("ALTER TABLE pagos
            ADD CONSTRAINT pagos_monto_chk CHECK (monto >= 0)");
        DB::statement("ALTER TABLE pagos
            ADD CONSTRAINT pagos_saldos_chk CHECK (
                (saldo_anterior IS NULL AND saldo_posterior IS NULL)
                OR (saldo_anterior >= 0 AND saldo_posterior >= 0)
            )");
 
        // Coherencia entre metodo y datos: si es tarjeta debe
        // haber tarjeta y saldos; si es efectivo, no.
        DB::statement("ALTER TABLE pagos
            ADD CONSTRAINT pagos_metodo_chk CHECK (
                (metodo = 'tarjeta'  AND tarjeta_id IS NOT NULL AND saldo_anterior IS NOT NULL)
                OR (metodo = 'efectivo' AND saldo_anterior IS NULL)
            )");
    }
    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::disableForeignKeyConstraints();
        Schema::dropIfExists('pagos');
        Schema::enableForeignKeyConstraints();
    }
};
