<?php
use Livewire\Attributes\Layout;

use Livewire\Volt\Component;
use App\Models\Venta;

new #[Layout('layouts.app')] class extends Component
{
    public Venta $venta;

    public function mount($id)
    {
        $this->venta = Venta::with([
            'user',
            'sucursal',
            'detalles.catalogo',
            'detalles.ventaSeries.serie',
        ])->findOrFail($id);
    }
}; ?>

<div>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <h2 class="text-xl font-semibold leading-tight text-gray-800">
                Venta: {{ $venta->folio }}
            </h2>
            <div class="flex gap-2">
                <button onclick="window.print()"
                    class="inline-flex items-center rounded-md border border-gray-300 bg-white px-4 py-2 text-sm font-medium text-gray-700 shadow-sm hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2">
                    Imprimir
                </button>
                <a href="{{ route('devoluciones.create', ['venta_id' => $venta->id]) }}" wire:navigate
                    class="inline-flex items-center rounded-md bg-orange-600 px-4 py-2 text-sm font-medium text-white shadow-sm hover:bg-orange-700 focus:outline-none focus:ring-2 focus:ring-orange-500 focus:ring-offset-2">
                    Devolver
                </a>
            </div>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="mx-auto max-w-4xl sm:px-6 lg:px-8">
            @if (session('success'))
                <div class="mb-4 rounded-md bg-green-50 p-4 text-sm text-green-800">
                    {{ session('success') }}
                </div>
            @endif

            <div class="overflow-hidden bg-white shadow-sm sm:rounded-lg">
                <div class="p-6">
                    <h3 class="mb-4 text-lg font-medium text-gray-900">Información de la Venta</h3>
                    <dl class="grid grid-cols-1 gap-4 sm:grid-cols-2">
                        <div>
                            <dt class="text-sm font-medium text-gray-500">Folio</dt>
                            <dd class="mt-1 text-sm text-gray-900">{{ $venta->folio }}</dd>
                        </div>
                        <div>
                            <dt class="text-sm font-medium text-gray-500">Fecha</dt>
                            <dd class="mt-1 text-sm text-gray-900">{{ $venta->created_at->format('d/m/Y H:i') }}</dd>
                        </div>
                        <div>
                            <dt class="text-sm font-medium text-gray-500">Cliente RUT</dt>
                            <dd class="mt-1 text-sm text-gray-900">{{ $venta->cliente_rut ?? '-' }}</dd>
                        </div>
                        <div>
                            <dt class="text-sm font-medium text-gray-500">Cliente Nombre</dt>
                            <dd class="mt-1 text-sm text-gray-900">{{ $venta->cliente_nombre ?? '-' }}</dd>
                        </div>
                        <div>
                            <dt class="text-sm font-medium text-gray-500">Usuario</dt>
                            <dd class="mt-1 text-sm text-gray-900">{{ $venta->user?->name }}</dd>
                        </div>
                        <div>
                            <dt class="text-sm font-medium text-gray-500">Sucursal</dt>
                            <dd class="mt-1 text-sm text-gray-900">{{ $venta->sucursal?->nombre }}</dd>
                        </div>
                    </dl>
                </div>
            </div>

            <div class="mt-6 overflow-hidden bg-white shadow-sm sm:rounded-lg">
                <div class="p-6">
                    <h3 class="mb-4 text-lg font-medium text-gray-900">Detalle de Productos</h3>
                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-200 text-sm">
                            <thead class="bg-gray-50">
                                <tr>
                                    <th class="px-4 py-3 text-left font-medium text-gray-500 uppercase tracking-wider">Producto</th>
                                    <th class="px-4 py-3 text-right font-medium text-gray-500 uppercase tracking-wider">Cantidad</th>
                                    <th class="px-4 py-3 text-right font-medium text-gray-500 uppercase tracking-wider">Precio Unitario</th>
                                    <th class="px-4 py-3 text-right font-medium text-gray-500 uppercase tracking-wider">Subtotal</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-200 bg-white">
                                @foreach ($venta->detalles as $detalle)
                                    <tr>
                                        <td class="px-4 py-3">
                                            <span class="font-medium">{{ $detalle->catalogo?->codigo }}</span>
                                            - {{ $detalle->catalogo?->nombre }}
                                            @if ($detalle->ventaSeries->count() > 0)
                                                <div class="mt-1 text-xs text-gray-500">
                                                    Series:
                                                    @foreach ($detalle->ventaSeries as $vs)
                                                        <span class="inline-block rounded bg-gray-100 px-1.5 py-0.5">{{ $vs->serie?->codigo_serie }}</span>
                                                    @endforeach
                                                </div>
                                            @endif
                                        </td>
                                        <td class="px-4 py-3 text-right">{{ $detalle->cantidad }}</td>
                                        <td class="px-4 py-3 text-right">${{ number_format($detalle->precio_unitario) }}</td>
                                        <td class="px-4 py-3 text-right">${{ number_format($detalle->subtotal) }}</td>
                                    </tr>
                                @endforeach
                            </tbody>
                            <tfoot class="bg-gray-50">
                                <tr>
                                    <td colspan="3" class="px-4 py-3 text-right font-medium text-gray-700">Total</td>
                                    <td class="px-4 py-3 text-right font-bold text-gray-900">${{ number_format($venta->total) }}</td>
                                </tr>
                            </tfoot>
                        </table>
                    </div>
                </div>
            </div>

            <div class="mt-6">
                <a href="{{ route('ventas.index') }}" wire:navigate
                    class="inline-flex items-center rounded-md border border-gray-300 bg-white px-4 py-2 text-sm font-medium text-gray-700 shadow-sm hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2">
                    &larr; Volver al historial
                </a>
            </div>
        </div>
    </div>
</div>


