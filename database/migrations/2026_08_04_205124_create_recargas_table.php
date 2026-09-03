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
        Schema::create('recargas', function (Blueprint $table) {
            $table->id();
            $table->foreignId('tarjeta_id')
                  ->constrained('tarjetas')->cascadeOnUpdate()->restrictOnDelete();
            $table->foreignId('parqueo_id')
                  ->constrained('parqueos')->cascadeOnUpdate()->restrictOnDelete();
            // Ya no apunta a `encargados` (esa tabla se elimino):
            // el encargado es un usuario con rol='encargado'.
            $table->foreignId('encargado_id')
                  ->constrained('usuarios')->cascadeOnUpdate()->restrictOnDelete();
 
            $table->decimal('monto', 8, 2);
            $table->decimal('saldo_anterior', 8, 2);
            $table->decimal('saldo_posterior', 8, 2);
            $table->enum('estado', ['exitosa', 'anulada'])->default('exitosa');
            $table->dateTime('fecha_recarga');
 
            $table->timestamps();
            $table->softDeletes();
 
            $table->unsignedBigInteger('created_by')->nullable();
            $table->unsignedBigInteger('updated_by')->nullable();
            $table->unsignedBigInteger('deleted_by')->nullable();
 
            $table->index(['parqueo_id', 'fecha_recarga'], 'recargas_parqueo_fecha_idx');
        });
 
        DB::statement("ALTER TABLE recargas
            ADD CONSTRAINT recargas_monto_chk CHECK (monto > 0)");
        DB::statement("ALTER TABLE recargas
            ADD CONSTRAINT recargas_saldos_chk
            CHECK (saldo_posterior > saldo_anterior)");
    }


    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::disableForeignKeyConstraints();
        Schema::dropIfExists('recargas');
        Schema::enableForeignKeyConstraints();
    }
};
