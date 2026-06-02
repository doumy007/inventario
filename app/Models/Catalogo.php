<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Catalogo extends Model
{
    protected $fillable = [
        'codigo',
        'nombre',
        'descripcion',
        'precio',
        'serie_habilitada',
    ];

    protected function casts(): array
    {
        return [
            'serie_habilitada' => 'boolean',
            'precio' => 'integer',
        ];
    }

    public function productos()
    {
        return $this->hasMany(Producto::class);
    }

    public function series()
    {
        return $this->hasMany(Serie::class);
    }

    public function ventaDetalles()
    {
        return $this->hasMany(VentaDetalle::class);
    }

    public function alertasReabastecimiento()
    {
        return $this->hasMany(AlertaReabastecimiento::class);
    }

    public function getStockAttribute()
    {
        if ($this->serie_habilitada) {
            return $this->series()->where('estado', 'disponible')->count();
        }

        return $this->productos()
            ->whereHas('ordenCompra', function ($q) {
                $q->where('estado', 'completada');
            })
            ->sum('cantidad');
    }
}
