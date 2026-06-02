<?php
use Livewire\Attributes\Layout;

use Livewire\Volt\Component;
use App\Models\Venta;
use App\Models\Catalogo;
use App\Models\Sucursal;

new #[Layout('layouts.app')] class extends Component
{
    public $fecha_desde = '';
    public $fecha_hasta = '';
    public $cliente_rut = '';
    public $catalogo_id = '';
    public $sucursal_id = '';

    public $ventas = [];
    public $totalSum = 0;

    public function with(): array
    {
        return [
            'catalogos' => Catalogo::orderBy('nombre')->get(),
            'sucursales' => Sucursal::where('activa', true)->orderBy('nombre')->get(),
        ];
    }

    public function filtrar()
    {
        $query = Venta::with('user', 'sucursal', 'detalles.catalogo');

        if ($this->fecha_desde) {
            $query->whereDate('created_at', '>=', $this->fecha_desde);
        }

        if ($this->fecha_hasta) {
            $query->whereDate('created_at', '<=', $this->fecha_hasta);
        }

        if ($this->cliente_rut) {
            $query->where('cliente_rut', 'like', '%' . $this->cliente_rut . '%');
        }

        if ($this->sucursal_id) {
            $query->where('sucursal_id', $this->sucursal_id);
        }

        if ($this->catalogo_id) {
            $query->whereHas('detalles', function ($q) {
                $q->where('catalogo_id', $this->catalogo_id);
            });
        }

        $this->ventas = $query->orderBy('created_at', 'desc')->get();
        $this->totalSum = $this->ventas->sum('total');
    }

    public function imprimir()
    {
        $this->dispatch('imprimir-pdf');
    }
}; ?>

<div>
    <x-slot name="header">
        <h2 class="text-xl font-semibold leading-tight text-gray-800">
            Reporte de Ventas
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="mx-auto max-w-7xl sm:px-6 lg:px-8">
            <div class="mb-6 overflow-hidden bg-white shadow-sm sm:rounded-lg">
                <div class="p-6">
                    <form wire:submit="filtrar" class="grid grid-cols-1 gap-4 md:grid-cols-3">
                        <div>
                            <label class="block text-sm font-medium text-gray-700">Fecha Desde</label>
                            <input
                                type="date"
                                wire:model="fecha_desde"
                                class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm"
                            >
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700">Fecha Hasta</label>
                            <input
                                type="date"
                                wire:model="fecha_hasta"
                                class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm"
                            >
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700">Cliente RUT</label>
                            <input
                                type="text"
                                wire:model="cliente_rut"
                                class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm"
                                placeholder="Buscar por RUT..."
                            >
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700">Producto (Catálogo)</label>
                            <select
                                wire:model="catalogo_id"
                                class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm"
                            >
                                <option value="">Todos</option>
                                @foreach ($catalogos as $catalogo)
                                    <option value="{{ $catalogo->id }}">{{ $catalogo->codigo }} - {{ $catalogo->nombre }}</option>
                                @endforeach
                            </select>
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700">Sucursal</label>
                            <select
                                wire:model="sucursal_id"
                                class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm"
                            >
                                <option value="">Todas</option>
                                @foreach ($sucursales as $sucursal)
                                    <option value="{{ $sucursal->id }}">{{ $sucursal->nombre }}</option>
                                @endforeach
                            </select>
                        </div>

                        <div class="flex items-end gap-2">
                            <button
                                type="submit"
                                class="inline-flex items-center rounded-md border border-transparent bg-indigo-600 px-4 py-2 text-sm font-medium text-white shadow-sm hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2"
                            >
                                Filtrar
                            </button>
                            <button
                                type="button"
                                wire:click="imprimir"
                                class="inline-flex items-center rounded-md border border-gray-300 bg-white px-4 py-2 text-sm font-medium text-gray-700 shadow-sm hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2"
                            >
                                Imprimir PDF
                            </button>
                        </div>
                    </form>
                </div>
            </div>

            @if (count($ventas) > 0)
                <div class="mb-4 rounded-md bg-indigo-50 p-4 text-sm text-indigo-800">
                    Total de ventas filtradas: <strong>${{ number_format($totalSum) }}</strong>
                </div>

                <div class="overflow-hidden bg-white shadow-sm sm:rounded-lg">
                    <div class="overflow-x-auto border-b border-gray-200">
                        <table class="min-w-full divide-y divide-gray-200 text-sm">
                            <thead class="bg-gray-50">
                                <tr>
                                    <th class="px-6 py-3 text-left font-medium text-gray-500 uppercase tracking-wider">Folio</th>
                                    <th class="px-6 py-3 text-left font-medium text-gray-500 uppercase tracking-wider">Fecha</th>
                                    <th class="px-6 py-3 text-left font-medium text-gray-500 uppercase tracking-wider">Cliente</th>
                                    <th class="px-6 py-3 text-right font-medium text-gray-500 uppercase tracking-wider">Total</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-200 bg-white">
                                @foreach ($ventas as $venta)
                                    <tr>
                                        <td class="whitespace-nowrap px-6 py-4 font-medium">{{ $venta->folio }}</td>
                                        <td class="whitespace-nowrap px-6 py-4">{{ $venta->created_at->format('d/m/Y H:i') }}</td>
                                        <td class="px-6 py-4">{{ $venta->cliente_nombre ?? $venta->cliente_rut ?? '—' }}</td>
                                        <td class="whitespace-nowrap px-6 py-4 text-right">${{ number_format($venta->total) }}</td>
                                    </tr>
                                @endforeach
                            </tbody>
                            <tfoot class="bg-gray-50">
                                <tr>
                                    <td colspan="3" class="px-6 py-3 text-right font-semibold text-gray-700">Total:</td>
                                    <td class="px-6 py-3 text-right font-bold text-gray-900">${{ number_format($totalSum) }}</td>
                                </tr>
                            </tfoot>
                        </table>
                    </div>
                </div>
            @elseif ($this->fecha_desde || $this->fecha_hasta || $this->cliente_rut || $this->catalogo_id || $this->sucursal_id)
                <div class="rounded-md bg-gray-50 p-4 text-center text-sm text-gray-500">
                    No se encontraron ventas con los filtros aplicados.
                </div>
            @endif
        </div>
    </div>

    <script>
        document.addEventListener('livewire:init', () => {
            Livewire.on('imprimir-pdf', () => {
                window.print();
            });
        });
    </script>
</div>


