<?php

use Livewire\Volt\Component;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;
use Livewire\Attributes\Layout;

new #[Layout('layouts.app')] class extends Component
{
    public $name = '';
    public $permissions = [];

    public function rules(): array
    {
        return [
            'name' => 'required|string|max:255|unique:roles,name',
            'permissions' => 'array',
        ];
    }

    public function save(): void
    {
        $this->validate();

        $role = Role::create(['name' => $this->name]);

        if (!empty($this->permissions)) {
            $role->syncPermissions($this->permissions);
        }

        session()->flash('message', 'Rol creado exitosamente.');
        $this->redirect(route('roles.index'), navigate: true);
    }

    public function with(): array
    {
        $permisos = Permission::all();
        $grupos = [];
        foreach ($permisos as $permiso) {
            $partes = explode('-', $permiso->name, 2);
            $modulo = $partes[0] ?? 'otros';
            $grupos[$modulo][] = $permiso;
        }

        return [
            'grupos' => $grupos,
        ];
    }
}; ?>

<div>
    <div class="flex justify-between items-center mb-6">
        <h1 class="text-2xl font-semibold text-gray-900">Crear Rol</h1>
        <a href="{{ route('roles.index') }}" wire:navigate>
            <x-secondary-button type="button">Volver</x-secondary-button>
        </a>
    </div>

    <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
        <div class="p-6 text-gray-900">
            @if (session('message'))
                <div class="mb-4 px-4 py-2 bg-green-100 border border-green-400 text-green-700 rounded">
                    {{ session('message') }}
                </div>
            @endif

            <form wire:submit="save" class="space-y-6">
                <div>
                    <x-input-label for="name" value="Nombre del Rol" />
                    <x-text-input wire:model="name" id="name" class="block mt-1 w-full" type="text" required />
                    <x-input-error :messages="$errors->get('name')" class="mt-2" />
                </div>

                <div>
                    <x-input-label value="Permisos" />
                    <div class="mt-2 space-y-4">
                        @foreach($grupos as $modulo => $permisos)
                        <div class="border rounded-lg p-4">
                            <h3 class="font-medium text-gray-700 mb-2 capitalize">{{ $modulo }}</h3>
                            <div class="grid grid-cols-2 md:grid-cols-3 gap-2">
                                @foreach($permisos as $permiso)
                                <label class="inline-flex items-center">
                                    <input wire:model="permissions" type="checkbox" value="{{ $permiso->name }}" class="rounded border-gray-300 text-indigo-600 shadow-sm focus:ring-indigo-500">
                                    <span class="ms-2 text-sm text-gray-600">{{ $permiso->name }}</span>
                                </label>
                                @endforeach
                            </div>
                        </div>
                        @endforeach
                    </div>
                    <x-input-error :messages="$errors->get('permissions')" class="mt-2" />
                </div>

                <div class="flex items-center gap-4">
                    <x-primary-button>Guardar</x-primary-button>
                </div>
            </form>
        </div>
    </div>
</div>
