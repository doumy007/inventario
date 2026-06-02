<?php
use Livewire\Attributes\Layout;

use Livewire\Volt\Component;
use Livewire\WithPagination;
use App\Models\Proveedor;

new #[Layout('layouts.app')] class extends Component
{
    use WithPagination;

    public function delete(Proveedor $proveedor)
    {
        $proveedor->delete();
        session()->flash('message', 'Proveedor eliminado correctamente');
    }

    public function with(): array
    {
        return [
            'proveedores' => Proveedor::orderBy('nombre')->paginate(10),
        ];
    }
}; ?>

<div>
    <x-slot name="header">
        <h2 class="text-xl font-semibold leading-tight text-gray-800">
            Proveedores
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
                <x-button-link href="{{ route('proveedores.create') }}" navigate>
                    Crear Proveedor
                </x-button-link>
            </div>

            <div class="overflow-hidden bg-white shadow-sm sm:rounded-lg">
                <div class="overflow-x-auto border-b border-gray-200">
                    <table class="min-w-full divide-y divide-gray-200 text-sm">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-6 py-3 text-left font-medium text-gray-500 uppercase tracking-wider">RUT</th>
                                <th class="px-6 py-3 text-left font-medium text-gray-500 uppercase tracking-wider">Nombre</th>
                                <th class="px-6 py-3 text-left font-medium text-gray-500 uppercase tracking-wider">Contacto</th>
                                <th class="px-6 py-3 text-left font-medium text-gray-500 uppercase tracking-wider">Teléfono</th>
                                <th class="px-6 py-3 text-left font-medium text-gray-500 uppercase tracking-wider">Email</th>
                                <th class="px-6 py-3 text-left font-medium text-gray-500 uppercase tracking-wider">Acciones</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-200 bg-white">
                            @forelse ($proveedores as $proveedor)
                                <tr>
                                    <td class="whitespace-nowrap px-6 py-4">{{ $proveedor->rut }}</td>
                                    <td class="whitespace-nowrap px-6 py-4">{{ $proveedor->nombre }}</td>
                                    <td class="whitespace-nowrap px-6 py-4">{{ $proveedor->contacto }}</td>
                                    <td class="whitespace-nowrap px-6 py-4">{{ $proveedor->telefono }}</td>
                                    <td class="whitespace-nowrap px-6 py-4">{{ $proveedor->email }}</td>
                                    <td class="whitespace-nowrap px-6 py-4">
                                        <div class="flex gap-2">
                                            <a href="{{ route('proveedores.edit', $proveedor->id) }}" navigate class="font-medium text-indigo-600 hover:text-indigo-900">
                                                Editar
                                            </a>
                                            <button
                                                x-data
                                                x-on:click="if (confirm('¿Eliminar el proveedor {{ $proveedor->nombre }}?')) $wire.delete({{ $proveedor->id }})"
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
                                        No hay proveedores registrados.
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
                @if ($proveedores->hasPages())
                    <div class="border-t border-gray-200 px-4 py-3">
                        {{ $proveedores->links() }}
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>


