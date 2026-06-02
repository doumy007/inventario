<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('productos', function (Blueprint $table) {
            $table->id();
            $table->foreignId('catalogo_id')->constrained('catalogos');
            $table->foreignId('orden_compra_id')->constrained('ordenes_compra');
            $table->foreignId('sucursal_id')->constrained('sucursales');
            $table->integer('cantidad')->default(1);
            $table->decimal('costo_unitario', 12, 0)->default(0);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('productos');
    }
};
