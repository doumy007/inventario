<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Venta extends Model
{
    protected $fillable = [
        'folio',
        'cliente_rut',
        'cliente_nombre',
        'subtotal',
        'total',
        'user_id',
        'sucursal_id',
    ];

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
        return $this->hasMany(VentaDetalle::class);
    }

    public function devoluciones()
    {
        return $this->hasMany(Devolucion::class);
    }
}
