<?php
use Livewire\Attributes\Layout;

use Livewire\Volt\Component;
use Livewire\WithPagination;
use App\Models\Catalogo;

new #[Layout('layouts.app')] class extends Component
{
    use WithPagination;

    public function delete(Catalogo $catalogo)
    {
        $catalogo->delete();
        session()->flash('message', 'Catálogo eliminado correctamente');
    }

    public function with(): array
    {
        return [
            'catalogos' => Catalogo::withCount('series')->orderBy('codigo')->paginate(10),
        ];
    }
}; ?>

<div>
    <x-slot name="header">
        <h2 class="text-xl font-semibold leading-tight text-gray-800">
            Catálogos
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
                <x-button-link href="{{ route('catalogos.create') }}" navigate>
                    Crear Catálogo
                </x-button-link>
            </div>

            <div class="overflow-hidden bg-white shadow-sm sm:rounded-lg">
                <div class="overflow-x-auto border-b border-gray-200">
                    <table class="min-w-full divide-y divide-gray-200 text-sm">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-6 py-3 text-left font-medium text-gray-500 uppercase tracking-wider">Código</th>
                                <th class="px-6 py-3 text-left font-medium text-gray-500 uppercase tracking-wider">Nombre</th>
                                <th class="px-6 py-3 text-left font-medium text-gray-500 uppercase tracking-wider">Precio</th>
                                <th class="px-6 py-3 text-left font-medium text-gray-500 uppercase tracking-wider">Serie Habilitada</th>
                                <th class="px-6 py-3 text-left font-medium text-gray-500 uppercase tracking-wider">Stock</th>
                                <th class="px-6 py-3 text-left font-medium text-gray-500 uppercase tracking-wider">Acciones</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-200 bg-white">
                            @forelse ($catalogos as $catalogo)
                                <tr>
                                    <td class="whitespace-nowrap px-6 py-4">{{ $catalogo->codigo }}</td>
                                    <td class="whitespace-nowrap px-6 py-4">{{ $catalogo->nombre }}</td>
                                    <td class="whitespace-nowrap px-6 py-4">${{ number_format($catalogo->precio) }}</td>
                                    <td class="whitespace-nowrap px-6 py-4">{{ $catalogo->serie_habilitada ? 'Sí' : 'No' }}</td>
                                    <td class="whitespace-nowrap px-6 py-4">{{ $catalogo->stock }}</td>
                                    <td class="whitespace-nowrap px-6 py-4">
                                        <div class="flex gap-2">
                                            <a href="{{ route('catalogos.edit', $catalogo->id) }}" navigate class="font-medium text-indigo-600 hover:text-indigo-900">
                                                Editar
                                            </a>
                                            <button
                                                x-data
                                                x-on:click="if (confirm('¿Eliminar el catálogo {{ $catalogo->nombre }}?')) $wire.delete({{ $catalogo->id }})"
                                                class="font-medium text-red-600 hover:text-red-900"
                                            >
                                                Eliminar
                                            </button>
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="6" class="px-6 py-4 text-center text-gray-500">
                                        No hay catálogos registrados.
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
                @if ($catalogos->hasPages())
                    <div class="border-t border-gray-200 px-4 py-3">
                        {{ $catalogos->links() }}
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>


