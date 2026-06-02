<?php
use Livewire\Attributes\Layout;

use App\Models\Sucursal;
use Livewire\Volt\Component;

new #[Layout('layouts.app')] class extends Component
{
    public string $nombre = '';
    public string $direccion = '';
    public string $telefono = '';
    public bool $activa = false;

    public function save(): void
    {
        $validated = $this->validate([
            'nombre' => ['required', 'string', 'max:255'],
            'direccion' => ['required', 'string', 'max:255'],
            'telefono' => ['required', 'string', 'max:20'],
            'activa' => ['boolean'],
        ]);

        Sucursal::create($validated);

        session()->flash('success', 'Sucursal creada correctamente.');

        $this->redirect(route('sucursales.index'), navigate: true);
    }
}; ?>

<div>
    <div class="mb-6">
        <h1 class="text-2xl font-bold text-gray-900">Crear Sucursal</h1>
    </div>

    <div class="bg-white rounded-lg shadow-md p-6 max-w-lg">
        <form wire:submit.prevent="save" class="space-y-6">
            <div>
                <label for="nombre" class="block text-sm font-medium text-gray-700">Nombre</label>
                <input wire:model="nombre" id="nombre" type="text" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                @error('nombre') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
            </div>

            <div>
                <label for="direccion" class="block text-sm font-medium text-gray-700">Dirección</label>
                <input wire:model="direccion" id="direccion" type="text" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                @error('direccion') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
            </div>

            <div>
                <label for="telefono" class="block text-sm font-medium text-gray-700">Teléfono</label>
                <input wire:model="telefono" id="telefono" type="text" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                @error('telefono') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
            </div>

            <div class="flex items-center">
                <input wire:model="activa" id="activa" type="checkbox" class="rounded border-gray-300 text-blue-600 shadow-sm focus:ring-blue-500">
                <label for="activa" class="ml-2 text-sm font-medium text-gray-700">Activa</label>
            </div>

            <div class="flex items-center gap-4">
                <button type="submit" class="bg-blue-600 hover:bg-blue-700 text-white font-medium py-2 px-4 rounded-lg transition duration-150">
                    Guardar
                </button>
                <a href="{{ route('sucursales.index') }}" wire:navigate class="text-gray-600 hover:text-gray-900">Cancelar</a>
            </div>
        </form>
    </div>
</div>


