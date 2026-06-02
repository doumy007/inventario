<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Devolucion extends Model
{
    protected $table = 'devoluciones';

    protected $fillable = [
        'folio',
        'venta_id',
        'motivo',
        'user_id',
        'sucursal_id',
    ];

    public function venta()
    {
        return $this->belongsTo(Venta::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function sucursal()
    {
        return $this->belongsTo(Sucursal::class);
    }

    public function detalles()
    {
        return $this->hasMany(DevolucionDetalle::class);
    }

    public function ordenesIngreso()
    {
        return $this->hasMany(OrdenIngreso::class);
    }
}
