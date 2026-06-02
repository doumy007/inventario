<?php
use Livewire\Attributes\Layout;

use Livewire\Volt\Component;
use Livewire\WithPagination;
use App\Models\Devolucion;

new #[Layout('layouts.app')] class extends Component
{
    use WithPagination;

    public function with(): array
    {
        return [
            'devoluciones' => Devolucion::with('venta', 'user')
                ->orderBy('created_at', 'desc')
                ->paginate(10),
        ];
    }
}; ?>

<div>
    <x-slot name="header">
        <h2 class="text-xl font-semibold leading-tight text-gray-800">
            Devoluciones
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
                <x-button-link href="{{ route('devoluciones.create') }}" navigate>
                    Nueva Devolución
                </x-button-link>
            </div>

            <div class="overflow-hidden bg-white shadow-sm sm:rounded-lg">
                <div class="overflow-x-auto border-b border-gray-200">
                    <table class="min-w-full divide-y divide-gray-200 text-sm">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-6 py-3 text-left font-medium text-gray-500 uppercase tracking-wider">Folio</th>
                                <th class="px-6 py-3 text-left font-medium text-gray-500 uppercase tracking-wider">Venta</th>
                                <th class="px-6 py-3 text-left font-medium text-gray-500 uppercase tracking-wider">Motivo</th>
                                <th class="px-6 py-3 text-left font-medium text-gray-500 uppercase tracking-wider">Usuario</th>
                                <th class="px-6 py-3 text-left font-medium text-gray-500 uppercase tracking-wider">Fecha</th>
                                <th class="px-6 py-3 text-left font-medium text-gray-500 uppercase tracking-wider">Acciones</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-200 bg-white">
                            @forelse ($devoluciones as $devolucion)
                                <tr>
                                    <td class="whitespace-nowrap px-6 py-4 font-medium">{{ $devolucion->folio }}</td>
                                    <td class="whitespace-nowrap px-6 py-4">{{ $devolucion->venta?->folio ?? '—' }}</td>
                                    <td class="px-6 py-4 max-w-xs truncate">{{ $devolucion->motivo }}</td>
                                    <td class="whitespace-nowrap px-6 py-4">{{ $devolucion->user?->name }}</td>
                                    <td class="whitespace-nowrap px-6 py-4">{{ $devolucion->created_at->format('d/m/Y H:i') }}</td>
                                    <td class="whitespace-nowrap px-6 py-4">
                                        <a href="{{ route('devoluciones.show', $devolucion->id) }}" navigate class="font-medium text-indigo-600 hover:text-indigo-900">
                                            Ver detalle
                                        </a>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="6" class="px-6 py-4 text-center text-gray-500">
                                        No hay devoluciones registradas.
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
                @if ($devoluciones->hasPages())
                    <div class="border-t border-gray-200 px-4 py-3">
                        {{ $devoluciones->links() }}
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>


