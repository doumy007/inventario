<?php
use Livewire\Attributes\Layout;

use Livewire\Volt\Component;
use App\Models\Devolucion;

new #[Layout('layouts.app')] class extends Component
{
    public Devolucion $devolucion;

    public function mount($id)
    {
        $this->devolucion = Devolucion::with([
            'venta',
            'user',
            'sucursal',
            'detalles.catalogo',
            'detalles.devolucionSeries.serie',
            'ordenesIngreso',
        ])->findOrFail($id);
    }
}; ?>

<div>
    <x-slot name="header">
        <h2 class="text-xl font-semibold leading-tight text-gray-800">
            Devolución: {{ $devolucion->folio }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="mx-auto max-w-4xl sm:px-6 lg:px-8">
            <div class="overflow-hidden bg-white shadow-sm sm:rounded-lg">
                <div class="p-6">
                    <div class="mb-6 grid grid-cols-2 gap-4">
                        <div>
                            <p class="text-sm text-gray-500">Folio</p>
                            <p class="font-medium">{{ $devolucion->folio }}</p>
                        </div>
                        <div>
                            <p class="text-sm text-gray-500">Venta</p>
                            <p class="font-medium">{{ $devolucion->venta?->folio ?? '—' }}</p>
                        </div>
                        <div>
                            <p class="text-sm text-gray-500">Usuario</p>
                            <p class="font-medium">{{ $devolucion->user?->name }}</p>
                        </div>
                        <div>
                            <p class="text-sm text-gray-500">Sucursal</p>
                            <p class="font-medium">{{ $devolucion->sucursal?->nombre }}</p>
                        </div>
                        <div>
                            <p class="text-sm text-gray-500">Fecha</p>
                            <p class="font-medium">{{ $devolucion->created_at->format('d/m/Y H:i') }}</p>
                        </div>
                        <div>
                            <p class="text-sm text-gray-500">Motivo</p>
                            <p class="font-medium">{{ $devolucion->motivo }}</p>
                        </div>
                    </div>

                    <h3 class="mb-3 text-lg font-semibold text-gray-800">Detalles Devueltos</h3>
                    <div class="mb-6 overflow-x-auto border-b border-gray-200">
                        <table class="min-w-full divide-y divide-gray-200 text-sm">
                            <thead class="bg-gray-50">
                                <tr>
                                    <th class="px-4 py-3 text-left font-medium text-gray-500 uppercase tracking-wider">Producto</th>
                                    <th class="px-4 py-3 text-center font-medium text-gray-500 uppercase tracking-wider">Cantidad</th>
                                    <th class="px-4 py-3 text-right font-medium text-gray-500 uppercase tracking-wider">Precio Unit.</th>
                                    <th class="px-4 py-3 text-right font-medium text-gray-500 uppercase tracking-wider">Subtotal</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-200 bg-white">
                                @forelse ($devolucion->detalles as $detalle)
                                    <tr>
                                        <td class="px-4 py-3">{{ $detalle->catalogo?->nombre }}</td>
                                        <td class="px-4 py-3 text-center">{{ $detalle->cantidad }}</td>
                                        <td class="px-4 py-3 text-right">${{ number_format($detalle->precio_unitario) }}</td>
                                        <td class="px-4 py-3 text-right">${{ number_format($detalle->subtotal) }}</td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="4" class="px-4 py-3 text-center text-gray-500">Sin detalles.</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>

                    @if ($devolucion->detalles->first(fn($d) => $d->devolucionSeries->isNotEmpty()))
                        <h3 class="mb-3 text-lg font-semibold text-gray-800">Series Devueltas</h3>
                        <div class="mb-6 overflow-x-auto border-b border-gray-200">
                            <table class="min-w-full divide-y divide-gray-200 text-sm">
                                <thead class="bg-gray-50">
                                    <tr>
                                        <th class="px-4 py-3 text-left font-medium text-gray-500 uppercase tracking-wider">Serie</th>
                                        <th class="px-4 py-3 text-left font-medium text-gray-500 uppercase tracking-wider">Producto</th>
                                    </tr>
                                </thead>
                                <tbody class="divide-y divide-gray-200 bg-white">
                                    @foreach ($devolucion->detalles as $detalle)
                                        @foreach ($detalle->devolucionSeries as $devSerie)
                                            <tr>
                                                <td class="px-4 py-3">{{ $devSerie->serie?->codigo_serie }}</td>
                                                <td class="px-4 py-3">{{ $detalle->catalogo?->nombre }}</td>
                                            </tr>
                                        @endforeach
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @endif

                    <h3 class="mb-3 text-lg font-semibold text-gray-800">Órdenes de Ingreso</h3>
                    <div class="mb-6 overflow-x-auto border-b border-gray-200">
                        <table class="min-w-full divide-y divide-gray-200 text-sm">
                            <thead class="bg-gray-50">
                                <tr>
                                    <th class="px-4 py-3 text-left font-medium text-gray-500 uppercase tracking-wider">Número</th>
                                    <th class="px-4 py-3 text-left font-medium text-gray-500 uppercase tracking-wider">Observación</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-200 bg-white">
                                @forelse ($devolucion->ordenesIngreso as $ingreso)
                                    <tr>
                                        <td class="px-4 py-3 font-medium">{{ $ingreso->numero_ingreso }}</td>
                                        <td class="px-4 py-3">{{ $ingreso->observacion }}</td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="2" class="px-4 py-3 text-center text-gray-500">Sin órdenes de ingreso.</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>

                    <div class="flex gap-4">
                        <a
                            href="{{ route('devoluciones.index') }}"
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


