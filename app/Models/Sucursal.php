<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Sucursal extends Model
{
    protected $table = 'sucursales';

    protected $fillable = [
        'nombre',
        'direccion',
        'telefono',
        'activa',
    ];

    protected function casts(): array
    {
        return [
            'activa' => 'boolean',
        ];
    }

    public function usuarios()
    {
        return $this->belongsToMany(User::class, 'sucursal_user')
            ->withPivot('predeterminada')
            ->withTimestamps();
    }

    public function ordenesCompra()
    {
        return $this->hasMany(OrdenCompra::class);
    }

    public function productos()
    {
        return $this->hasMany(Producto::class);
    }

    public function series()
    {
        return $this->hasMany(Serie::class);
    }

    public function ventas()
    {
        return $this->hasMany(Venta::class);
    }

    public function devoluciones()
    {
        return $this->hasMany(Devolucion::class);
    }

    public function alertasReabastecimiento()
    {
        return $this->hasMany(AlertaReabastecimiento::class);
    }
}
