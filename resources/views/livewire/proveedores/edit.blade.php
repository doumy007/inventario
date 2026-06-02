<?php
use Livewire\Attributes\Layout;

use Livewire\Volt\Component;
use App\Models\Proveedor;
use Illuminate\Validation\Rule;

new #[Layout('layouts.app')] class extends Component
{
    public Proveedor $proveedor;
    public $rut = '';
    public $nombre = '';
    public $contacto = '';
    public $telefono = '';
    public $email = '';
    public $direccion = '';

    public function mount($id)
    {
        $this->proveedor = Proveedor::findOrFail($id);
        $this->rut = $this->proveedor->rut;
        $this->nombre = $this->proveedor->nombre;
        $this->contacto = $this->proveedor->contacto;
        $this->telefono = $this->proveedor->telefono;
        $this->email = $this->proveedor->email;
        $this->direccion = $this->proveedor->direccion;
    }

    public function rules()
    {
        return [
            'rut' => ['required', 'string', 'max:20', Rule::unique('proveedores', 'rut')->ignore($this->proveedor->id)],
            'nombre' => ['required', 'string', 'max:255'],
            'contacto' => ['nullable', 'string', 'max:255'],
            'telefono' => ['nullable', 'string', 'max:50'],
            'email' => ['nullable', 'email', 'max:255'],
            'direccion' => ['nullable', 'string', 'max:500'],
        ];
    }

    public function messages()
    {
        return [
            'rut.required' => 'El RUT es obligatorio.',
            'rut.unique' => 'El RUT ya está registrado.',
            'nombre.required' => 'El nombre es obligatorio.',
        ];
    }

    public function save()
    {
        $this->validate();

        $this->proveedor->update([
            'rut' => $this->rut,
            'nombre' => $this->nombre,
            'contacto' => $this->contacto,
            'telefono' => $this->telefono,
            'email' => $this->email,
            'direccion' => $this->direccion,
        ]);

        session()->flash('message', 'Proveedor actualizado correctamente');
        $this->redirect(route('proveedores.index'), navigate: true);
    }
}; ?>

<div>
    <x-slot name="header">
        <h2 class="text-xl font-semibold leading-tight text-gray-800">
            Editar Proveedor: {{ $proveedor->nombre }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="mx-auto max-w-4xl sm:px-6 lg:px-8">
            <div class="overflow-hidden bg-white shadow-sm sm:rounded-lg">
                <div class="p-6">
                    <form wire:submit="save" class="space-y-6">
                        <div>
                            <label for="rut" class="block text-sm font-medium text-gray-700">RUT</label>
                            <input
                                id="rut"
                                type="text"
                                wire:model="rut"
                                class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm"
                            >
                            @error('rut') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
                        </div>

                        <div>
                            <label for="nombre" class="block text-sm font-medium text-gray-700">Nombre</label>
                            <input
                                id="nombre"
                                type="text"
                                wire:model="nombre"
                                class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm"
                            >
                            @error('nombre') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
                        </div>

                        <div>
                            <label for="contacto" class="block text-sm font-medium text-gray-700">Contacto</label>
                            <input
                                id="contacto"
                                type="text"
                                wire:model="contacto"
                                class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm"
                            >
                            @error('contacto') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
                        </div>

                        <div>
                            <label for="telefono" class="block text-sm font-medium text-gray-700">Teléfono</label>
                            <input
                                id="telefono"
                                type="text"
                                wire:model="telefono"
                                class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm"
                            >
                            @error('telefono') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
                        </div>

                        <div>
                            <label for="email" class="block text-sm font-medium text-gray-700">Email</label>
                            <input
                                id="email"
                                type="email"
                                wire:model="email"
                                class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm"
                            >
                            @error('email') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
                        </div>

                        <div>
                            <label for="direccion" class="block text-sm font-medium text-gray-700">Dirección</label>
                            <textarea
                                id="direccion"
                                wire:model="direccion"
                                rows="3"
                                class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm"
                            ></textarea>
                            @error('direccion') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
                        </div>

                        <div class="flex gap-4">
                            <button
                                type="submit"
                                class="inline-flex items-center rounded-md border border-transparent bg-indigo-600 px-4 py-2 text-sm font-medium text-white shadow-sm hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2"
                            >
                                Actualizar
                            </button>
                            <a
                                href="{{ route('proveedores.index') }}"
                                navigate
                                class="inline-flex items-center rounded-md border border-gray-300 bg-white px-4 py-2 text-sm font-medium text-gray-700 shadow-sm hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2"
                            >
                                Cancelar
                            </a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>


