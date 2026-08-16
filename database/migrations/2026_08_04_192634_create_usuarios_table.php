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
        Schema::create('usuarios', function (Blueprint $table) {
            $table->id();
            $table->string('nombre', 100);
            $table->string('apellido', 100)->nullable();
            $table->string('ci', 20)->nullable();
            $table->string('email', 150)->nullable();
            $table->string('password')->nullable();
            $table->rememberToken();
            $table->enum('rol', ['encargado', 'usuario'])->default('usuario');
            $table->string('telefono', 20)->nullable();
            $table->enum('categoria', ['estudiante', 'docente', 'administrativo', 'visitante'])->nullable();
 
            // parqueo que ADMINISTRA (solo encargados)
            $table->foreignId('parqueo_asignado_id')->nullable()
                  ->constrained('parqueos')->cascadeOnUpdate()->nullOnDelete();
            // parqueo que lo REGISTRO por primera vez
            $table->foreignId('parqueo_origen_id')->nullable()
                  ->constrained('parqueos')->cascadeOnUpdate()->nullOnDelete();
 
            $table->tinyInteger('activo')->default(1);
 
            $table->timestamps();
            $table->softDeletes();
 
            $table->unsignedBigInteger('created_by')->nullable();
            $table->unsignedBigInteger('updated_by')->nullable();
            $table->unsignedBigInteger('deleted_by')->nullable();
 
            // Vale 1 si el registro esta vivo, NULL si fue borrado.
            // Permite que los UNIQUE convivan con el soft delete.
            $table->tinyInteger('vigente')->storedAs('IF(deleted_at IS NULL, 1, NULL)');
 
            $table->unique(['ci', 'vigente'], 'usuarios_ci_uq');
            $table->unique(['email', 'vigente'], 'usuarios_email_uq');
            $table->index('nombre', 'usuarios_nombre_idx');
        });
 
        DB::statement("ALTER TABLE usuarios
            ADD CONSTRAINT usuarios_rol_chk
            CHECK (rol <> 'encargado' OR parqueo_asignado_id IS NOT NULL)");
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::disableForeignKeyConstraints();
        Schema::dropIfExists('usuarios');
        Schema::enableForeignKeyConstraints();
    }
};