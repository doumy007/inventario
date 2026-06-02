<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class DevolucionSerie extends Model
{
    protected $table = 'devolucion_series';

    protected $fillable = [
        'devolucion_detalle_id',
        'serie_id',
    ];

    public function devolucionDetalle()
    {
        return $this->belongsTo(DevolucionDetalle::class);
    }

    public function serie()
    {
        return $this->belongsTo(Serie::class);
    }
}
