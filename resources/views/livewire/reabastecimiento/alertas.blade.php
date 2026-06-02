<?php
use Livewire\Attributes\Layout;

use Livewire\Volt\Component;
use App\Models\AlertaReabastecimiento;
use App\Models\Catalogo;
use App\Models\Sucursal;

new #[Layout('layouts.app')] class extends Component
{
    public $edit_id = null;
    public $catalogo_id = '';
    public $sucursal_id = '';
    public $cantidad_minima = 0;
    public $tope_maximo = 0;

    public function rules()
    {
        return [
            'catalogo_id' => ['required', 'exists:catalogos,id'],
            'sucursal_id' => ['required', 'exists:sucursales,id'],
            'cantidad_minima' => ['required', 'integer', 'min:0'],
            'tope_maximo' => ['required', 'integer', 'min:0'],
        ];
    }

    public function messages()
    {
        return [
            'catalogo_id.required' => 'Seleccione un catálogo.',
            'sucursal_id.required' => 'Seleccione una sucursal.',
            'cantidad_minima.required' => 'La cantidad mínima es obligatoria.',
            'cantidad_minima.integer' => 'Debe ser un número entero.',
            'tope_maximo.required' => 'El tope máximo es obligatorio.',
            'tope_maximo.integer' => 'Debe ser un número entero.',
        ];
    }

    public function with(): array
    {
        return [
            'alertas' => AlertaReabastecimiento::with('catalogo', 'sucursal')
                ->orderBy('created_at', 'desc')
                ->get(),
            'catalogos' => Catalogo::orderBy('nombre')->get(),
            'sucursales' => Sucursal::where('activa', true)->orderBy('nombre')->get(),
        ];
    }

    public function save()
    {
        $this->validate();

        $data = [
            'catalogo_id' => $this->catalogo_id,
            'sucursal_id' => $this->sucursal_id,
            'cantidad_minima' => $this->cantidad_minima,
            'tope_maximo' => $this->tope_maximo,
        ];

        if ($this->edit_id) {
            $alerta = AlertaReabastecimiento::findOrFail($this->edit_id);
            $alerta->update($data);
            session()->flash('message', 'Alerta actualizada correctamente.');
        } else {
            AlertaReabastecimiento::create($data);
            session()->flash('message', 'Alerta creada correctamente.');
        }

        $this->resetForm();
    }

    public function edit(AlertaReabastecimiento $alerta)
    {
        $this->edit_id = $alerta->id;
        $this->catalogo_id = (string) $alerta->catalogo_id;
        $this->sucursal_id = (string) $alerta->sucursal_id;
        $this->cantidad_minima = $alerta->cantidad_minima;
        $this->tope_maximo = $alerta->tope_maximo;
    }

    public function delete(AlertaReabastecimiento $alerta)
    {
        $alerta->delete();
        session()->flash('message', 'Alerta eliminada correctamente.');
    }

    public function toggleActiva(AlertaReabastecimiento $alerta)
    {
        $alerta->update(['activa' => !$alerta->activa]);
    }

    public function resetForm()
    {
        $this->edit_id = null;
        $this->catalogo_id = '';
        $this->sucursal_id = '';
        $this->cantidad_minima = 0;
        $this->tope_maximo = 0;
    }

    private function getStock($catalogo, $sucursalId)
    {
        if ($catalogo->serie_habilitada) {
            return $catalogo->series()->where('sucursal_id', $sucursalId)->where('estado', 'disponible')->count();
        }
        return $catalogo->productos()
            ->where('sucursal_id', $sucursalId)
            ->whereHas('ordenCompra', fn($q) => $q->where('estado', 'completada'))
            ->sum('cantidad');
    }
}; ?>

<div>
    <x-slot name="header">
        <h2 class="text-xl font-semibold leading-tight text-gray-800">
            Alertas de Reabastecimiento
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="mx-auto max-w-7xl sm:px-6 lg:px-8">
            @if (session('message'))
                <div class="mb-4 rounded-md bg-green-50 p-4 text-sm text-green-800">
                    {{ session('message') }}
                </div>
            @endif

            <div class="mb-6 overflow-hidden bg-white shadow-sm sm:rounded-lg">
                <div class="p-6">
                    <h3 class="mb-4 text-lg font-semibold text-gray-800">
                        {{ $edit_id ? 'Editar Alerta' : 'Nueva Alerta' }}
                    </h3>
                    <form wire:submit="save" class="grid grid-cols-1 gap-4 md:grid-cols-5">
                        <div>
                            <label class="block text-sm font-medium text-gray-700">Catálogo</label>
                            <select
                                wire:model="catalogo_id"
                                class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm"
                            >
                                <option value="">Seleccione...</option>
                                @foreach ($catalogos as $catalogo)
                                    <option value="{{ $catalogo->id }}">{{ $catalogo->codigo }} - {{ $catalogo->nombre }}</option>
                                @endforeach
                            </select>
                            @error('catalogo_id') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700">Sucursal</label>
                            <select
                                wire:model="sucursal_id"
                                class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm"
                            >
                                <option value="">Seleccione...</option>
                                @foreach ($sucursales as $sucursal)
                                    <option value="{{ $sucursal->id }}">{{ $sucursal->nombre }}</option>
                                @endforeach
                            </select>
                            @error('sucursal_id') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700">Cant. Mínima</label>
                            <input
                                type="number"
                                wire:model="cantidad_minima"
                                min="0"
                                class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm"
                            >
                            @error('cantidad_minima') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700">Tope Máximo</label>
                            <input
                                type="number"
                                wire:model="tope_maximo"
                                min="0"
                                class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm"
                            >
                            @error('tope_maximo') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
                        </div>

                        <div class="flex items-end gap-2">
                            <button
                                type="submit"
                                class="inline-flex items-center rounded-md border border-transparent bg-indigo-600 px-4 py-2 text-sm font-medium text-white shadow-sm hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2"
                            >
                                {{ $edit_id ? 'Actualizar' : 'Guardar' }}
                            </button>
                            @if ($edit_id)
                                <button
                                    type="button"
                                    wire:click="resetForm"
                                    class="inline-flex items-center rounded-md border border-gray-300 bg-white px-4 py-2 text-sm font-medium text-gray-700 shadow-sm hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2"
                                >
                                    Cancelar
                                </button>
                            @endif
                        </div>
                    </form>
                </div>
            </div>

            <div class="overflow-hidden bg-white shadow-sm sm:rounded-lg">
                <div class="overflow-x-auto border-b border-gray-200">
                    <table class="min-w-full divide-y divide-gray-200 text-sm">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-6 py-3 text-left font-medium text-gray-500 uppercase tracking-wider">Catálogo</th>
                                <th class="px-6 py-3 text-left font-medium text-gray-500 uppercase tracking-wider">Sucursal</th>
                                <th class="px-6 py-3 text-center font-medium text-gray-500 uppercase tracking-wider">Cant. Mínima</th>
                                <th class="px-6 py-3 text-center font-medium text-gray-500 uppercase tracking-wider">Tope Máximo</th>
                                <th class="px-6 py-3 text-center font-medium text-gray-500 uppercase tracking-wider">Stock Actual</th>
                                <th class="px-6 py-3 text-center font-medium text-gray-500 uppercase tracking-wider">Activa</th>
                                <th class="px-6 py-3 text-center font-medium text-gray-500 uppercase tracking-wider">Acciones</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-200 bg-white">
                            @forelse ($alertas as $alerta)
                                @php
                                    $stock = $this->getStock($alerta->catalogo, $alerta->sucursal_id);
                                    $bajo = $stock <= $alerta->cantidad_minima;
                                @endphp
                                <tr class="{{ $bajo && $alerta->activa ? 'bg-red-50' : '' }}">
                                    <td class="whitespace-nowrap px-6 py-4">
                                        {{ $alerta->catalogo?->codigo }} - {{ $alerta->catalogo?->nombre }}
                                    </td>
                                    <td class="whitespace-nowrap px-6 py-4">{{ $alerta->sucursal?->nombre }}</td>
                                    <td class="whitespace-nowrap px-6 py-4 text-center">{{ $alerta->cantidad_minima }}</td>
                                    <td class="whitespace-nowrap px-6 py-4 text-center">{{ $alerta->tope_maximo }}</td>
                                    <td class="whitespace-nowrap px-6 py-4 text-center font-semibold {{ $bajo && $alerta->activa ? 'text-red-600' : '' }}">
                                        {{ $stock }}
                                    </td>
                                    <td class="whitespace-nowrap px-6 py-4 text-center">
                                        <button
                                            wire:click="toggleActiva({{ $alerta->id }})"
                                            class="inline-flex items-center rounded-full px-2.5 py-0.5 text-xs font-medium {{ $alerta->activa ? 'bg-green-100 text-green-800' : 'bg-gray-100 text-gray-600' }}"
                                        >
                                            {{ $alerta->activa ? 'Sí' : 'No' }}
                                        </button>
                                    </td>
                                    <td class="whitespace-nowrap px-6 py-4 text-center">
                                        <div class="flex justify-center gap-2">
                                            <button
                                                wire:click="edit({{ $alerta->id }})"
                                                class="font-medium text-indigo-600 hover:text-indigo-900"
                                            >
                                                Editar
                                            </button>
                                            <button
                                                x-data
                                                x-on:click="if (confirm('¿Eliminar esta alerta?')) $wire.delete({{ $alerta->id }})"
                                                class="font-medium text-red-600 hover:text-red-900"
                                            >
                                                Eliminar
                                            </button>
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="7" class="px-6 py-4 text-center text-gray-500">
                                        No hay alertas configuradas.
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>


