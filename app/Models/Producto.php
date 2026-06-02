<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Producto extends Model
{
    protected $fillable = [
        'catalogo_id',
        'orden_compra_id',
        'sucursal_id',
        'cantidad',
        'costo_unitario',
    ];

    public function catalogo()
    {
        return $this->belongsTo(Catalogo::class);
    }

    public function ordenCompra()
    {
        return $this->belongsTo(OrdenCompra::class);
    }

    public function sucursal()
    {
        return $this->belongsTo(Sucursal::class);
    }

    public function series()
    {
        return $this->hasMany(Serie::class);
    }
}
