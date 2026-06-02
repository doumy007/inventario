<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class VentaSerie extends Model
{
    protected $table = 'venta_series';

    protected $fillable = [
        'venta_detalle_id',
        'serie_id',
    ];

    public function ventaDetalle()
    {
        return $this->belongsTo(VentaDetalle::class);
    }

    public function serie()
    {
        return $this->belongsTo(Serie::class);
    }
}
