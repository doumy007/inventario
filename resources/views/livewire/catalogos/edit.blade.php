<?php
use Livewire\Attributes\Layout;

use Livewire\Volt\Component;
use App\Models\Catalogo;
use Illuminate\Validation\Rule;

new #[Layout('layouts.app')] class extends Component
{
    public Catalogo $catalogo;
    public $codigo = '';
    public $nombre = '';
    public $descripcion = '';
    public $precio = 0;
    public $serie_habilitada = false;

    public function mount($id)
    {
        $this->catalogo = Catalogo::findOrFail($id);
        $this->codigo = $this->catalogo->codigo;
        $this->nombre = $this->catalogo->nombre;
        $this->descripcion = $this->catalogo->descripcion;
        $this->precio = $this->catalogo->precio;
        $this->serie_habilitada = $this->catalogo->serie_habilitada;
    }

    public function rules()
    {
        return [
            'codigo' => ['required', 'string', 'max:50', Rule::unique('catalogos', 'codigo')->ignore($this->catalogo->id)],
            'nombre' => ['required', 'string', 'max:255'],
            'descripcion' => ['nullable', 'string'],
            'precio' => ['required', 'integer', 'min:0'],
            'serie_habilitada' => ['boolean'],
        ];
    }

    public function messages()
    {
        return [
            'codigo.required' => 'El código es obligatorio.',
            'codigo.unique' => 'El código ya está en uso.',
            'nombre.required' => 'El nombre es obligatorio.',
            'precio.required' => 'El precio es obligatorio.',
            'precio.integer' => 'El precio debe ser un número entero.',
        ];
    }

    public function save()
    {
        $this->validate();

        $this->catalogo->update([
            'codigo' => $this->codigo,
            'nombre' => $this->nombre,
            'descripcion' => $this->descripcion,
            'precio' => $this->precio,
            'serie_habilitada' => $this->serie_habilitada,
        ]);

        session()->flash('message', 'Catálogo actualizado correctamente');
        $this->redirect(route('catalogos.index'), navigate: true);
    }
}; ?>

<div>
    <x-slot name="header">
        <h2 class="text-xl font-semibold leading-tight text-gray-800">
            Editar Catálogo: {{ $catalogo->nombre }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="mx-auto max-w-4xl sm:px-6 lg:px-8">
            <div class="overflow-hidden bg-white shadow-sm sm:rounded-lg">
                <div class="p-6">
                    <form wire:submit="save" class="space-y-6">
                        <div>
                            <label for="codigo" class="block text-sm font-medium text-gray-700">Código</label>
                            <input
                                id="codigo"
                                type="text"
                                wire:model="codigo"
                                class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm"
                            >
                            @error('codigo') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
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
                            <label for="descripcion" class="block text-sm font-medium text-gray-700">Descripción</label>
                            <textarea
                                id="descripcion"
                                wire:model="descripcion"
                                rows="3"
                                class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm"
                            ></textarea>
                            @error('descripcion') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
                        </div>

                        <div>
                            <label for="precio" class="block text-sm font-medium text-gray-700">Precio</label>
                            <input
                                id="precio"
                                type="number"
                                wire:model="precio"
                                min="0"
                                class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm"
                            >
                            @error('precio') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
                        </div>

                        <div class="flex items-center gap-2">
                            <input
                                id="serie_habilitada"
                                type="checkbox"
                                wire:model="serie_habilitada"
                                class="rounded border-gray-300 text-indigo-600 shadow-sm focus:ring-indigo-500"
                            >
                            <label for="serie_habilitada" class="text-sm font-medium text-gray-700">Serie Habilitada</label>
                            @error('serie_habilitada') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
                        </div>

                        <div class="flex gap-4">
                            <button
                                type="submit"
                                class="inline-flex items-center rounded-md border border-transparent bg-indigo-600 px-4 py-2 text-sm font-medium text-white shadow-sm hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2"
                            >
                                Actualizar
                            </button>
                            <a
                                href="{{ route('catalogos.index') }}"
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


