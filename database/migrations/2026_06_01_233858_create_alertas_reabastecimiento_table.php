<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('alertas_reabastecimiento', function (Blueprint $table) {
            $table->id();
            $table->foreignId('catalogo_id')->constrained('catalogos');
            $table->foreignId('sucursal_id')->constrained('sucursales');
            $table->integer('cantidad_minima')->default(0);
            $table->integer('tope_maximo')->default(0);
            $table->boolean('activa')->default(true);
            $table->timestamps();
            $table->unique(['catalogo_id', 'sucursal_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('alertas_reabastecimiento');
    }
};
