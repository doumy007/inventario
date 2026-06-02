<?php

use Livewire\Volt\Component;
use App\Models\OrdenCompra;
use Livewire\Attributes\Layout;

new #[Layout('layouts.app')] class extends Component
{
    public OrdenCompra $orden;

    public function mount($id)
    {
        $this->orden = OrdenCompra::with([
            'proveedor',
            'sucursal',
            'productos.catalogo',
            'productos.series',
        ])->findOrFail($id);
    }

    public function with(): array
    {
        $productosAgrupados = $this->orden->productos->groupBy(fn($p) => $p->catalogo->nombre);

        return [
            'productosAgrupados' => $productosAgrupados,
        ];
    }
}; ?>

<div>
    <x-slot name="header">
        <h2 class="text-xl font-semibold leading-tight text-gray-800">
            Detalle de Orden de Compra
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="mx-auto max-w-4xl sm:px-6 lg:px-8">
            <div class="overflow-hidden bg-white shadow-sm sm:rounded-lg">
                <div class="p-6">
                    <dl class="grid grid-cols-1 gap-4 sm:grid-cols-2">
                        <div>
                            <dt class="text-sm font-medium text-gray-500">N° Orden</dt>
                            <dd class="mt-1 text-sm text-gray-900">{{ $orden->numero_orden }}</dd>
                        </div>
                        <div>
                            <dt class="text-sm font-medium text-gray-500">Proveedor</dt>
                            <dd class="mt-1 text-sm text-gray-900">{{ $orden->proveedor->nombre }}</dd>
                        </div>
                        <div>
                            <dt class="text-sm font-medium text-gray-500">Sucursal</dt>
                            <dd class="mt-1 text-sm text-gray-900">{{ $orden->sucursal->nombre }}</dd>
                        </div>
                        <div>
                            <dt class="text-sm font-medium text-gray-500">Fecha</dt>
                            <dd class="mt-1 text-sm text-gray-900">{{ $orden->fecha->format('d/m/Y') }}</dd>
                        </div>
                        <div>
                            <dt class="text-sm font-medium text-gray-500">Estado</dt>
                            <dd class="mt-1 text-sm text-gray-900">{{ $orden->estado }}</dd>
                        </div>
                        @if ($orden->observacion)
                            <div class="sm:col-span-2">
                                <dt class="text-sm font-medium text-gray-500">Observación</dt>
                                <dd class="mt-1 text-sm text-gray-900">{{ $orden->observacion }}</dd>
                            </div>
                        @endif
                    </dl>
                </div>
            </div>

            <div class="mt-6 overflow-hidden bg-white shadow-sm sm:rounded-lg">
                <div class="p-6">
                    <h3 class="text-lg font-medium text-gray-900">Productos</h3>
                    <div class="mt-4 space-y-6">
                        @forelse ($productosAgrupados as $catalogoNombre => $productos)
                            <div class="rounded-md border border-gray-200 p-4">
                                <h4 class="text-base font-medium text-gray-800">{{ $catalogoNombre }}</h4>
                                <div class="mt-3 overflow-x-auto">
                                    <table class="min-w-full text-sm">
                                        <thead>
                                            <tr class="border-b border-gray-200">
                                                <th class="px-4 py-2 text-left font-medium text-gray-500">Cantidad</th>
                                                <th class="px-4 py-2 text-left font-medium text-gray-500">Costo Unitario</th>
                                                <th class="px-4 py-2 text-left font-medium text-gray-500">Total</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @foreach ($productos as $producto)
                                                <tr class="border-b border-gray-100">
                                                    <td class="px-4 py-2">{{ $producto->cantidad }}</td>
                                                    <td class="px-4 py-2">${{ number_format($producto->costo_unitario) }}</td>
                                                    <td class="px-4 py-2">${{ number_format($producto->cantidad * $producto->costo_unitario) }}</td>
                                                </tr>
                                            @endforeach
                                        </tbody>
                                    </table>
                                </div>

                                @php
                                    $primerProducto = $productos->first();
                                @endphp
                                @if ($primerProducto->catalogo->serie_habilitada && $primerProducto->series->isNotEmpty())
                                    <div class="mt-3">
                                        <h5 class="text-sm font-medium text-gray-600">Códigos de Serie</h5>
                                        <div class="mt-1 flex flex-wrap gap-1">
                                            @foreach ($primerProducto->series as $serie)
                                                <span class="inline-flex items-center rounded-full bg-gray-100 px-2.5 py-0.5 text-xs font-medium text-gray-800">
                                                    {{ $serie->codigo_serie }}
                                                </span>
                                            @endforeach
                                        </div>
                                    </div>
                                @endif
                            </div>
                        @empty
                            <p class="text-sm text-gray-500">No hay productos en esta orden.</p>
                        @endforelse
                    </div>

                    <div class="mt-6">
                        <a
                            href="{{ route('ordenes-compra.index') }}"
                            navigate
                            class="inline-flex items-center rounded-md border border-gray-300 bg-white px-4 py-2 text-sm font-medium text-gray-700 shadow-sm hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2"
                        >
                            Volver
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
