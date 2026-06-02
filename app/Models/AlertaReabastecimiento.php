<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AlertaReabastecimiento extends Model
{
    protected $table = 'alertas_reabastecimiento';

    protected $fillable = [
        'catalogo_id',
        'sucursal_id',
        'cantidad_minima',
        'tope_maximo',
        'activa',
    ];

    protected function casts(): array
    {
        return [
            'activa' => 'boolean',
        ];
    }

    public function catalogo()
    {
        return $this->belongsTo(Catalogo::class);
    }

    public function sucursal()
    {
        return $this->belongsTo(Sucursal::class);
    }
}
