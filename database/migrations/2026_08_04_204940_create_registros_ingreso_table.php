<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // La estadia. El cobro vive en la tabla `pagos`.
        Schema::create('registros_ingreso', function (Blueprint $table) {
            $table->id();
            $table->foreignId('parqueo_id')
                  ->constrained('parqueos')->cascadeOnUpdate()->restrictOnDelete();
            // NULLABLE: un visitante ocasional no tiene tarjeta
            // ni vehiculo registrado en el sistema.
            $table->foreignId('tarjeta_id')->nullable()
                  ->constrained('tarjetas')->cascadeOnUpdate()->restrictOnDelete();
            $table->foreignId('vehiculo_id')->nullable()
                  ->constrained('vehiculos')->cascadeOnUpdate()->restrictOnDelete();
            $table->foreignId('espacio_id')->nullable()
                  ->constrained('espacios')->cascadeOnUpdate()->nullOnDelete();
 
            // Datos del visitante sin tarjeta (ingreso manual).
            // Se llenan solo cuando tarjeta_id y vehiculo_id son NULL.
            $table->string('placa_visitante', 15)->nullable();
            $table->enum('tipo_visitante', ['moto', 'auto'])->nullable();
 
            $table->dateTime('hora_ingreso');
            $table->dateTime('hora_salida')->nullable();
            // Periodos del dia que ocupo: base del cobro (1, 2 o 3)
            $table->unsignedTinyInteger('periodos_usados')->nullable();
            $table->enum('estado', ['activo', 'finalizado'])->default('activo');
 
            $table->timestamps();
            $table->softDeletes();
 
            $table->unsignedBigInteger('created_by')->nullable();
            $table->unsignedBigInteger('updated_by')->nullable();
            $table->unsignedBigInteger('deleted_by')->nullable();
 
            $table->index(['parqueo_id', 'estado'], 'registros_parqueo_estado_idx');
            $table->index(['parqueo_id', 'hora_ingreso'], 'registros_parqueo_fecha_idx');
        });
 
        DB::statement("ALTER TABLE registros_ingreso
            ADD CONSTRAINT registros_salida_chk
            CHECK (hora_salida IS NULL OR hora_salida >= hora_ingreso)");
        DB::statement("ALTER TABLE registros_ingreso
            ADD CONSTRAINT registros_periodos_chk
            CHECK (periodos_usados IS NULL OR periodos_usados BETWEEN 1 AND 3)");
 
        // Todo ingreso debe tener origen: o es un cliente con
        // tarjeta y vehiculo registrado, o es un visitante con
        // placa y tipo. Nunca ninguno de los dos.
        DB::statement("ALTER TABLE registros_ingreso
            ADD CONSTRAINT registros_origen_chk CHECK (
                (tarjeta_id IS NOT NULL AND vehiculo_id IS NOT NULL)
                OR (placa_visitante IS NOT NULL AND tipo_visitante IS NOT NULL)
            )");
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::disableForeignKeyConstraints();
        Schema::dropIfExists('registros_ingreso');
        Schema::enableForeignKeyConstraints();
    }
};
