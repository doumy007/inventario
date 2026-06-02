<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class DevolucionDetalle extends Model
{
    protected $table = 'devolucion_detalles';

    protected $fillable = [
        'devolucion_id',
        'catalogo_id',
        'cantidad',
        'precio_unitario',
        'subtotal',
    ];

    public function devolucion()
    {
        return $this->belongsTo(Devolucion::class);
    }

    public function catalogo()
    {
        return $this->belongsTo(Catalogo::class);
    }

    public function devolucionSeries()
    {
        return $this->hasMany(DevolucionSerie::class);
    }
}
