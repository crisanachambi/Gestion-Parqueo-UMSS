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
        / Una tarjeta por usuario Y parqueo, con su propio saldo.
        // Reemplaza a la antigua tabla `saldos`.
        Schema::create('tarjetas', function (Blueprint $table) {
            $table->id();
            $table->foreignId('usuario_id')
                  ->constrained('usuarios')->cascadeOnUpdate()->restrictOnDelete();
            $table->foreignId('parqueo_id')
                  ->constrained('parqueos')->cascadeOnUpdate()->restrictOnDelete();
            $table->string('codigo_rfid', 32);
            $table->decimal('saldo', 8, 2)->default(0);
            $table->enum('estado', ['activa', 'bloqueada'])->default('activa');
 
            $table->timestamps();
            $table->softDeletes();
 
            $table->unsignedBigInteger('created_by')->nullable();
            $table->unsignedBigInteger('updated_by')->nullable();
            $table->unsignedBigInteger('deleted_by')->nullable();
 
            $table->tinyInteger('vigente')->storedAs('IF(deleted_at IS NULL, 1, NULL)');
 
            $table->unique(['codigo_rfid', 'vigente'], 'tarjetas_rfid_uq');
            $table->unique(['usuario_id', 'parqueo_id', 'vigente'], 'tarjetas_usuario_parqueo_uq');
        });
 
        DB::statement("ALTER TABLE tarjetas
            ADD CONSTRAINT tarjetas_saldo_chk CHECK (saldo >= 0)");
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::disableForeignKeyConstraints();
        Schema::dropIfExists('tarjetas');
        Schema::enableForeignKeyConstraints();
    }
};
