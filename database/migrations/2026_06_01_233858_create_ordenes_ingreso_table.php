<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('ordenes_ingreso', function (Blueprint $table) {
            $table->id();
            $table->string('numero_ingreso', 50)->unique();
            $table->foreignId('devolucion_id')->constrained('devoluciones');
            $table->foreignId('sucursal_id')->constrained('sucursales');
            $table->text('observacion')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('ordenes_ingreso');
    }
};
