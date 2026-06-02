<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('devolucion_series', function (Blueprint $table) {
            $table->id();
            $table->foreignId('devolucion_detalle_id')->constrained('devolucion_detalles')->cascadeOnDelete();
            $table->foreignId('serie_id')->constrained('series');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('devolucion_series');
    }
};
