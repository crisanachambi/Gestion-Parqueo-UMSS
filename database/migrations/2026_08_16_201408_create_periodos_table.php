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
        Schema::create('periodos', function (Blueprint $table) {
            $table->id();
            $table->foreignId('parqueo_id')
                  ->constrained('parqueos')->cascadeOnUpdate()->restrictOnDelete();
            $table->string('nombre', 40);           // manana / tarde / noche
            $table->time('hora_inicio');
            $table->time('hora_fin');
            $table->unsignedTinyInteger('orden');   // 1, 2 o 3
 
            $table->timestamps();
            $table->softDeletes();
 
            $table->unsignedBigInteger('created_by')->nullable();
            $table->unsignedBigInteger('updated_by')->nullable();
            $table->unsignedBigInteger('deleted_by')->nullable();
        });
 
        DB::statement("ALTER TABLE periodos
            ADD CONSTRAINT periodos_horas_chk CHECK (hora_fin > hora_inicio)");
        DB::statement("ALTER TABLE periodos
            ADD CONSTRAINT periodos_orden_chk CHECK (orden BETWEEN 1 AND 3)");
    }
 
    public function down(): void
    {
        Schema::disableForeignKeyConstraints();
        Schema::dropIfExists('periodos');
        Schema::enableForeignKeyConstraints();
    }
};
