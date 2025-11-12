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
        Schema::create('sesiones_estadisticas', function (Blueprint $table) {
    $table->id();
    $table->string('nombre_clave')->nullable(); // Ej: sesionx, agrupada_2025_11_11_01
    $table->enum('tipo_serie', ['simple', 'agrupada'])->nullable();
    $table->text('descripcion')->nullable(); // Opcional: notas del usuario
    $table->timestamps();
});
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('sesiones_estadisticas');
    }
};
