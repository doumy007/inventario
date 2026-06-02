<?php
use Livewire\Attributes\Layout;

use Livewire\Volt\Component;
use Livewire\WithPagination;
use App\Models\Venta;
use Carbon\Carbon;

new #[Layout('layouts.app')] class extends Component
{
    use WithPagination;

    public $desde = '';
    public $hasta = '';

    public function filter()
    {
        $this->resetPage();
    }

    public function with(): array
    {
        $query = Venta::with(['user', 'sucursal'])->orderBy('created_at', 'desc');

        if ($this->desde) {
            $query->whereDate('created_at', '>=', Carbon::parse($this->desde));
        }

        if ($this->hasta) {
            $query->whereDate('created_at', '<=', Carbon::parse($this->hasta));
        }

        return [
            'ventas' => $query->paginate(15),
        ];
    }
}; ?>

<div>
    <x-slot name="header">
        <h2 class="text-xl font-semibold leading-tight text-gray-800">
            Historial de Ventas
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="mx-auto max-w-7xl sm:px-6 lg:px-8">
            @if (session('success'))
                <div class="mb-4 rounded-md bg-green-50 p-4 text-sm text-green-800">
                    {{ session('success') }}
                </div>
            @endif

            <div class="mb-4 flex items-center justify-between">
                <div class="flex gap-4 items-end">
                    <div>
                        <label class="block text-sm font-medium text-gray-700">Desde</label>
                        <input type="date" wire:model.live="desde" wire:change="filter"
                            class="mt-1 block rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700">Hasta</label>
                        <input type="date" wire:model.live="hasta" wire:change="filter"
                            class="mt-1 block rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm">
                    </div>
                </div>
                <a href="{{ route('ventas.create') }}" wire:navigate
                    class="inline-flex items-center rounded-md bg-indigo-600 px-4 py-2 text-sm font-medium text-white shadow-sm hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2">
                    + Nueva Venta
                </a>
            </div>

            <div class="overflow-hidden bg-white shadow-sm sm:rounded-lg">
                <div class="overflow-x-auto border-b border-gray-200">
                    <table class="min-w-full divide-y divide-gray-200 text-sm">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-6 py-3 text-left font-medium text-gray-500 uppercase tracking-wider">Folio</th>
                                <th class="px-6 py-3 text-left font-medium text-gray-500 uppercase tracking-wider">Cliente RUT</th>
                                <th class="px-6 py-3 text-left font-medium text-gray-500 uppercase tracking-wider">Cliente Nombre</th>
                                <th class="px-6 py-3 text-left font-medium text-gray-500 uppercase tracking-wider">Total</th>
                                <th class="px-6 py-3 text-left font-medium text-gray-500 uppercase tracking-wider">Usuario</th>
                                <th class="px-6 py-3 text-left font-medium text-gray-500 uppercase tracking-wider">Sucursal</th>
                                <th class="px-6 py-3 text-left font-medium text-gray-500 uppercase tracking-wider">Fecha</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-200 bg-white">
                            @forelse ($ventas as $venta)
                                <tr
                                    class="cursor-pointer hover:bg-gray-50"
                                    wire:click="redirect({{ route('ventas.show', $venta->id) }})"
                                >
                                    <td class="whitespace-nowrap px-6 py-4 font-medium">{{ $venta->folio }}</td>
                                    <td class="whitespace-nowrap px-6 py-4">{{ $venta->cliente_rut ?? '-' }}</td>
                                    <td class="whitespace-nowrap px-6 py-4">{{ $venta->cliente_nombre ?? '-' }}</td>
                                    <td class="whitespace-nowrap px-6 py-4">${{ number_format($venta->total) }}</td>
                                    <td class="whitespace-nowrap px-6 py-4">{{ $venta->user?->name }}</td>
                                    <td class="whitespace-nowrap px-6 py-4">{{ $venta->sucursal?->nombre }}</td>
                                    <td class="whitespace-nowrap px-6 py-4">{{ $venta->created_at->format('d/m/Y H:i') }}</td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="7" class="px-6 py-4 text-center text-gray-500">
                                        No hay ventas registradas.
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
                @if ($ventas->hasPages())
                    <div class="border-t border-gray-200 px-4 py-3">
                        {{ $ventas->links() }}
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>


