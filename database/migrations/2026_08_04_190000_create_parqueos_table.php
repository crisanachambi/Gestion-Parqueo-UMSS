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
        Schema::create('parqueos', function (Blueprint $table) {
            $table->id();
            $table->string('nombre', 120);
            $table->string('ubicacion', 180)->nullable();
            $table->decimal('tarifa_auto', 6, 2)->default(4.00);  // Bs por periodo
            $table->decimal('tarifa_moto', 6, 2)->default(1.00);  // Bs por periodo
            // Jornada completa. Los `periodos` la subdividen en 3.
            $table->time('horario_apertura')->default('06:30:00');
            $table->time('horario_cierre')->default('21:30:00');
 
            // Reglas configurables por parqueo (valores, no logica).
            // Tolerancia en 0 = se cobra siempre, minimo un periodo.
            // Si la universidad decide perdonar estadias cortas,
            // el encargado sube este valor desde el panel.
            $table->unsignedSmallInteger('tolerancia_minutos')->default(0);
            // Se aplica si el vehiculo se queda despues del cierre.
            $table->decimal('multa_nocturna', 6, 2)->default(0.00);
 
            $table->tinyInteger('activo')->default(1);
 
            $table->timestamps();
            $table->softDeletes();
 
            // Auditoria SIN foreign key: los asigna la aplicacion.
            $table->unsignedBigInteger('created_by')->nullable();
            $table->unsignedBigInteger('updated_by')->nullable();
            $table->unsignedBigInteger('deleted_by')->nullable();
        });
 
        DB::statement("ALTER TABLE parqueos
            ADD CONSTRAINT parqueos_tarifas_chk
            CHECK (tarifa_auto >= 0 AND tarifa_moto >= 0)");
 
        DB::statement("ALTER TABLE parqueos
            ADD CONSTRAINT parqueos_horario_chk
            CHECK (horario_cierre > horario_apertura)");
    }
    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::disableForeignKeyConstraints();
        Schema::dropIfExists('parqueos');
        Schema::enableForeignKeyConstraints();
    }
};
