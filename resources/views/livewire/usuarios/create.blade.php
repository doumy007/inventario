<?php

use Livewire\Volt\Component;
use App\Models\User;
use App\Models\Sucursal;
use Spatie\Permission\Models\Role;
use Livewire\Attributes\Layout;

new #[Layout('layouts.app')] class extends Component
{
    public $name = '';
    public $email = '';
    public $password = '';
    public $password_confirmation = '';
    public $rut = '';
    public $telefono = '';
    public $activo = true;
    public $roles = [];
    public $sucursales = [];

    public function rules(): array
    {
        return [
            'name' => 'required|string|max:255',
            'email' => 'required|email|max:255|unique:users,email',
            'password' => 'required|string|min:8|confirmed',
            'rut' => 'nullable|string|max:20',
            'telefono' => 'nullable|string|max:20',
            'activo' => 'boolean',
            'roles' => 'array',
            'sucursales' => 'array',
        ];
    }

    public function save(): void
    {
        $this->validate();

        $user = User::create([
            'name' => $this->name,
            'email' => $this->email,
            'password' => $this->password,
            'rut' => $this->rut,
            'telefono' => $this->telefono,
            'activo' => $this->activo,
        ]);

        if (!empty($this->roles)) {
            $user->syncRoles($this->roles);
        }

        if (!empty($this->sucursales)) {
            $user->sucursales()->sync($this->sucursales);
        }

        session()->flash('message', 'Usuario creado exitosamente.');
        $this->redirect(route('usuarios.index'), navigate: true);
    }

    public function with(): array
    {
        return [
            'rolesDisponibles' => Role::all(),
            'sucursalesDisponibles' => Sucursal::all(),
        ];
    }
}; ?>

<div>
    <div class="flex justify-between items-center mb-6">
        <h1 class="text-2xl font-semibold text-gray-900">Crear Usuario</h1>
        <a href="{{ route('usuarios.index') }}" wire:navigate>
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
                    <x-input-label for="name" value="Nombre" />
                    <x-text-input wire:model="name" id="name" class="block mt-1 w-full" type="text" required />
                    <x-input-error :messages="$errors->get('name')" class="mt-2" />
                </div>

                <div>
                    <x-input-label for="email" value="Email" />
                    <x-text-input wire:model="email" id="email" class="block mt-1 w-full" type="email" required />
                    <x-input-error :messages="$errors->get('email')" class="mt-2" />
                </div>

                <div>
                    <x-input-label for="password" value="Contraseña" />
                    <x-text-input wire:model="password" id="password" class="block mt-1 w-full" type="password" required />
                    <x-input-error :messages="$errors->get('password')" class="mt-2" />
                </div>

                <div>
                    <x-input-label for="password_confirmation" value="Confirmar Contraseña" />
                    <x-text-input wire:model="password_confirmation" id="password_confirmation" class="block mt-1 w-full" type="password" required />
                    <x-input-error :messages="$errors->get('password_confirmation')" class="mt-2" />
                </div>

                <div>
                    <x-input-label for="rut" value="RUT" />
                    <x-text-input wire:model="rut" id="rut" class="block mt-1 w-full" type="text" />
                    <x-input-error :messages="$errors->get('rut')" class="mt-2" />
                </div>

                <div>
                    <x-input-label for="telefono" value="Teléfono" />
                    <x-text-input wire:model="telefono" id="telefono" class="block mt-1 w-full" type="text" />
                    <x-input-error :messages="$errors->get('telefono')" class="mt-2" />
                </div>

                <div>
                    <label class="inline-flex items-center">
                        <input wire:model="activo" type="checkbox" class="rounded border-gray-300 text-indigo-600 shadow-sm focus:ring-indigo-500" checked>
                        <span class="ms-2 text-sm text-gray-600">Activo</span>
                    </label>
                    <x-input-error :messages="$errors->get('activo')" class="mt-2" />
                </div>

                <div>
                    <x-input-label value="Roles" />
                    <div class="mt-2 grid grid-cols-2 md:grid-cols-3 gap-2">
                        @foreach($rolesDisponibles as $role)
                        <label class="inline-flex items-center">
                            <input wire:model="roles" type="checkbox" value="{{ $role->name }}" class="rounded border-gray-300 text-indigo-600 shadow-sm focus:ring-indigo-500">
                            <span class="ms-2 text-sm text-gray-600">{{ $role->name }}</span>
                        </label>
                        @endforeach
                    </div>
                    <x-input-error :messages="$errors->get('roles')" class="mt-2" />
                </div>

                <div>
                    <x-input-label value="Sucursales" />
                    <div class="mt-2 grid grid-cols-2 md:grid-cols-3 gap-2">
                        @foreach($sucursalesDisponibles as $sucursal)
                        <label class="inline-flex items-center">
                            <input wire:model="sucursales" type="checkbox" value="{{ $sucursal->id }}" class="rounded border-gray-300 text-indigo-600 shadow-sm focus:ring-indigo-500">
                            <span class="ms-2 text-sm text-gray-600">{{ $sucursal->nombre }}</span>
                        </label>
                        @endforeach
                    </div>
                    <x-input-error :messages="$errors->get('sucursales')" class="mt-2" />
                </div>

                <div class="flex items-center gap-4">
                    <x-primary-button>Guardar</x-primary-button>
                </div>
            </form>
        </div>
    </div>
</div>
