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
      Schema::create('clases_agrupadas_rt', function (Blueprint $table) {
    $table->id();
    $table->decimal('limite_inferior', 10, 4)->nullable();
    $table->decimal('limite_superior', 10, 4)->nullable();
    $table->unsignedInteger('frecuencia')->nullable();
    $table->timestamps();
});
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('clases_agrupadas_rt');
    }
};
