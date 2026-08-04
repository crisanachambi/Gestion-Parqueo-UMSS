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
        Schema::create('parqueos', function (Blueprint $table) {
            $table->id();
            $table->string('nombre');
            $table->string('ubicacion')->nullable();
            $table->integer('capacidad_total')->default(0);
            $table->decimal('tarifa_moto_periodo', 8, 2)->default(0);  // tarifa por período (moto)
            $table->decimal('tarifa_auto_periodo', 8, 2)->default(0);  // tarifa por período (auto)
            $table->time('horario_apertura')->nullable();
            $table->time('horario_cierre')->nullable();

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
        Schema::dropIfExists('parqueos');
    }
};
