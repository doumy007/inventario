<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class OrdenIngreso extends Model
{
    protected $table = 'ordenes_ingreso';

    protected $fillable = [
        'numero_ingreso',
        'devolucion_id',
        'sucursal_id',
        'observacion',
    ];

    public function devolucion()
    {
        return $this->belongsTo(Devolucion::class);
    }

    public function sucursal()
    {
        return $this->belongsTo(Sucursal::class);
    }
}
