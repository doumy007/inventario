<?php

use Livewire\Volt\Component;
use Livewire\WithPagination;
use App\Models\OrdenCompra;
use Livewire\Attributes\Layout;

new #[Layout('layouts.app')] class extends Component
{
    use WithPagination;

    public function with(): array
    {
        return [
            'ordenes' => OrdenCompra::with('proveedor:id,nombre', 'sucursal:id,nombre')
                ->withCount('productos')
                ->orderByDesc('fecha')
                ->paginate(10),
        ];
    }
}; ?>

<div>
    <x-slot name="header">
        <h2 class="text-xl font-semibold leading-tight text-gray-800">
            Órdenes de Compra
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="mx-auto max-w-7xl sm:px-6 lg:px-8">
            @if (session('message'))
                <div class="mb-4 rounded-md bg-green-50 p-4 text-sm text-green-800">
                    {{ session('message') }}
                </div>
            @endif

            <div class="mb-4">
                <x-button-link href="{{ route('ordenes-compra.create') }}" navigate>
                    Crear Orden de Compra
                </x-button-link>
            </div>

            <div class="overflow-hidden bg-white shadow-sm sm:rounded-lg">
                <div class="overflow-x-auto border-b border-gray-200">
                    <table class="min-w-full divide-y divide-gray-200 text-sm">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-6 py-3 text-left font-medium text-gray-500 uppercase tracking-wider">N° Orden</th>
                                <th class="px-6 py-3 text-left font-medium text-gray-500 uppercase tracking-wider">Proveedor</th>
                                <th class="px-6 py-3 text-left font-medium text-gray-500 uppercase tracking-wider">Sucursal</th>
                                <th class="px-6 py-3 text-left font-medium text-gray-500 uppercase tracking-wider">Fecha</th>
                                <th class="px-6 py-3 text-left font-medium text-gray-500 uppercase tracking-wider">Estado</th>
                                <th class="px-6 py-3 text-left font-medium text-gray-500 uppercase tracking-wider">Total Productos</th>
                                <th class="px-6 py-3 text-left font-medium text-gray-500 uppercase tracking-wider">Acciones</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-200 bg-white">
                            @forelse ($ordenes as $orden)
                                <tr>
                                    <td class="whitespace-nowrap px-6 py-4">{{ $orden->numero_orden }}</td>
                                    <td class="whitespace-nowrap px-6 py-4">{{ $orden->proveedor->nombre }}</td>
                                    <td class="whitespace-nowrap px-6 py-4">{{ $orden->sucursal->nombre }}</td>
                                    <td class="whitespace-nowrap px-6 py-4">{{ $orden->fecha->format('d/m/Y') }}</td>
                                    <td class="whitespace-nowrap px-6 py-4">{{ $orden->estado }}</td>
                                    <td class="whitespace-nowrap px-6 py-4">{{ $orden->productos_count }}</td>
                                    <td class="whitespace-nowrap px-6 py-4">
                                        <a href="{{ route('ordenes-compra.show', $orden->id) }}" navigate class="font-medium text-indigo-600 hover:text-indigo-900">
                                            Ver Detalle
                                        </a>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="7" class="px-6 py-4 text-center text-gray-500">
                                        No hay órdenes de compra registradas.
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
                @if ($ordenes->hasPages())
                    <div class="border-t border-gray-200 px-4 py-3">
                        {{ $ordenes->links() }}
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>
