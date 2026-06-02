<?php
use Livewire\Attributes\Layout;

use Livewire\Volt\Component;
use App\Models\Catalogo;
use App\Models\Sucursal;

new #[Layout('layouts.app')] class extends Component
{
    public $sucursal_id = '';

    public function with(): array
    {
        $catalogos = Catalogo::orderBy('codigo')
            ->with(['series' => function ($q) {
                if ($this->sucursal_id) {
                    $q->where('sucursal_id', $this->sucursal_id);
                }
            }, 'productos' => function ($q) {
                if ($this->sucursal_id) {
                    $q->where('sucursal_id', $this->sucursal_id);
                }
                $q->whereHas('ordenCompra', function ($oq) {
                    $oq->where('estado', 'completada');
                });
            }]);

        return [
            'catalogos' => $catalogos->get()->map(function ($c) {
                $stock = 0;
                if ($c->serie_habilitada) {
                    $stock = $c->series->where('estado', 'disponible')->count();
                } else {
                    $stock = $c->productos->sum('cantidad');
                }
                $c->stock_calculado = $stock;
                return $c;
            }),
            'sucursales' => Sucursal::where('activa', true)->orderBy('nombre')->get(),
        ];
    }
}; ?>

<div>
    <x-slot name="header">
        <h2 class="text-xl font-semibold leading-tight text-gray-800">
            Stock de Inventario
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="mx-auto max-w-7xl sm:px-6 lg:px-8">
            <div class="mb-4">
                <label class="block text-sm font-medium text-gray-700">Filtrar por Sucursal</label>
                <select
                    wire:model="sucursal_id"
                    wire:change="$refresh"
                    class="mt-1 block w-64 rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm"
                >
                    <option value="">Todas las sucursales</option>
                    @foreach ($sucursales as $sucursal)
                        <option value="{{ $sucursal->id }}">{{ $sucursal->nombre }}</option>
                    @endforeach
                </select>
            </div>

            <div class="overflow-hidden bg-white shadow-sm sm:rounded-lg">
                <div class="overflow-x-auto border-b border-gray-200">
                    <table class="min-w-full divide-y divide-gray-200 text-sm">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-6 py-3 text-left font-medium text-gray-500 uppercase tracking-wider">Código</th>
                                <th class="px-6 py-3 text-left font-medium text-gray-500 uppercase tracking-wider">Nombre</th>
                                <th class="px-6 py-3 text-center font-medium text-gray-500 uppercase tracking-wider">Stock Actual</th>
                                <th class="px-6 py-3 text-right font-medium text-gray-500 uppercase tracking-wider">Precio</th>
                                <th class="px-6 py-3 text-center font-medium text-gray-500 uppercase tracking-wider">Alerta</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-200 bg-white">
                            @forelse ($catalogos as $catalogo)
                                @php
                                    $alerta = $catalogo->alertasReabastecimiento
                                        ->where('activa', true)
                                        ->first(fn($a) => !$this->sucursal_id || $a->sucursal_id == $this->sucursal_id);
                                @endphp
                                <tr class="{{ $alerta && $catalogo->stock_calculado <= $alerta->cantidad_minima ? 'bg-red-50' : '' }}">
                                    <td class="whitespace-nowrap px-6 py-4">{{ $catalogo->codigo }}</td>
                                    <td class="whitespace-nowrap px-6 py-4">{{ $catalogo->nombre }}</td>
                                    <td class="whitespace-nowrap px-6 py-4 text-center font-semibold">
                                        {{ $catalogo->stock_calculado }}
                                    </td>
                                    <td class="whitespace-nowrap px-6 py-4 text-right">${{ number_format($catalogo->precio) }}</td>
                                    <td class="whitespace-nowrap px-6 py-4 text-center">
                                        @if ($alerta && $catalogo->stock_calculado <= $alerta->cantidad_minima)
                                            <span class="inline-flex items-center rounded-full bg-red-100 px-2.5 py-0.5 text-xs font-medium text-red-800">
                                                Stock bajo (mín: {{ $alerta->cantidad_minima }})
                                            </span>
                                        @elseif ($alerta)
                                            <span class="inline-flex items-center rounded-full bg-green-100 px-2.5 py-0.5 text-xs font-medium text-green-800">
                                                OK
                                            </span>
                                        @else
                                            <span class="text-gray-400">—</span>
                                        @endif
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="5" class="px-6 py-4 text-center text-gray-500">
                                        No hay productos en el inventario.
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>


