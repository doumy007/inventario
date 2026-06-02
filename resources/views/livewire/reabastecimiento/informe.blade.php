<?php
use Livewire\Attributes\Layout;

use Livewire\Volt\Component;
use App\Models\AlertaReabastecimiento;
use App\Models\Catalogo;

new #[Layout('layouts.app')] class extends Component
{
    public function with(): array
    {
        $alertas = AlertaReabastecimiento::with('catalogo', 'sucursal')
            ->where('activa', true)
            ->get();

        $items = [];

        foreach ($alertas as $alerta) {
            $catalogo = $alerta->catalogo;
            if (!$catalogo) continue;

            $stock = $this->getStock($catalogo, $alerta->sucursal_id);
            $aComprar = max(0, $alerta->tope_maximo - $stock);

            if ($aComprar > 0) {
                $items[] = (object) [
                    'catalogo_codigo' => $catalogo->codigo,
                    'catalogo_nombre' => $catalogo->nombre,
                    'stock_actual' => $stock,
                    'tope_maximo' => $alerta->tope_maximo,
                    'a_comprar' => $aComprar,
                    'sucursal_nombre' => $alerta->sucursal?->nombre,
                ];
            }
        }

        return [
            'items' => $items,
        ];
    }

    private function getStock(Catalogo $catalogo, int $sucursalId): int
    {
        if ($catalogo->serie_habilitada) {
            return $catalogo->series()
                ->where('sucursal_id', $sucursalId)
                ->where('estado', 'disponible')
                ->count();
        }

        return $catalogo->productos()
            ->where('sucursal_id', $sucursalId)
            ->whereHas('ordenCompra', fn($q) => $q->where('estado', 'completada'))
            ->sum('cantidad');
    }

    public function exportar()
    {
        $this->dispatch('exportar-informe');
    }
}; ?>

<div>
    <x-slot name="header">
        <h2 class="text-xl font-semibold leading-tight text-gray-800">
            Informe de Reabastecimiento
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="mx-auto max-w-7xl sm:px-6 lg:px-8">
            @if (count($items) > 0)
                <div class="mb-4 flex justify-end">
                    <button
                        wire:click="exportar"
                        class="inline-flex items-center rounded-md border border-transparent bg-indigo-600 px-4 py-2 text-sm font-medium text-white shadow-sm hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2"
                    >
                        Exportar
                    </button>
                </div>

                <div class="overflow-hidden bg-white shadow-sm sm:rounded-lg">
                    <div class="overflow-x-auto border-b border-gray-200">
                        <table class="min-w-full divide-y divide-gray-200 text-sm">
                            <thead class="bg-gray-50">
                                <tr>
                                    <th class="px-6 py-3 text-left font-medium text-gray-500 uppercase tracking-wider">Catálogo</th>
                                    <th class="px-6 py-3 text-center font-medium text-gray-500 uppercase tracking-wider">Stock Actual</th>
                                    <th class="px-6 py-3 text-center font-medium text-gray-500 uppercase tracking-wider">Tope Máximo</th>
                                    <th class="px-6 py-3 text-center font-medium text-gray-500 uppercase tracking-wider">Cant. a Comprar</th>
                                    <th class="px-6 py-3 text-left font-medium text-gray-500 uppercase tracking-wider">Sucursal</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-200 bg-white">
                                @foreach ($items as $item)
                                    <tr>
                                        <td class="whitespace-nowrap px-6 py-4">
                                            {{ $item->catalogo_codigo }} - {{ $item->catalogo_nombre }}
                                        </td>
                                        <td class="whitespace-nowrap px-6 py-4 text-center">{{ $item->stock_actual }}</td>
                                        <td class="whitespace-nowrap px-6 py-4 text-center">{{ $item->tope_maximo }}</td>
                                        <td class="whitespace-nowrap px-6 py-4 text-center font-semibold text-indigo-600">
                                            {{ $item->a_comprar }}
                                        </td>
                                        <td class="whitespace-nowrap px-6 py-4">{{ $item->sucursal_nombre }}</td>
                                    </tr>
                                @endforeach
                            </tbody>
                            <tfoot class="bg-gray-50">
                                <tr>
                                    <td colspan="4" class="px-6 py-3 text-right font-semibold text-gray-700">Total a comprar:</td>
                                    <td class="px-6 py-3 font-bold text-gray-900">{{ collect($items)->sum('a_comprar') }}</td>
                                </tr>
                            </tfoot>
                        </table>
                    </div>
                </div>
            @else
                <div class="rounded-md bg-gray-50 p-4 text-center text-sm text-gray-500">
                    No hay productos que necesiten reabastecimiento en este momento.
                </div>
            @endif
        </div>
    </div>

    <script>
        document.addEventListener('livewire:init', () => {
            Livewire.on('exportar-informe', () => {
                window.print();
            });
        });
    </script>
</div>


