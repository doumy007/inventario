<?php

use Livewire\Volt\Component;
use App\Models\OrdenCompra;
use App\Models\Producto;
use App\Models\Serie;
use App\Models\Catalogo;
use App\Models\Proveedor;
use Illuminate\Validation\Rule;
use Livewire\Attributes\Layout;

new #[Layout('layouts.app')] class extends Component
{
    public $numero_orden = '';
    public $proveedor_id = '';
    public $sucursal_id = '';
    public $fecha = '';
    public $observacion = '';
    public $detalles = [];

    public function mount()
    {
        $this->fecha = now()->format('Y-m-d');
    }

    public function addDetalle()
    {
        $this->detalles[] = [
            'catalogo_id' => '',
            'cantidad' => 1,
            'costo_unitario' => 0,
            'series' => '',
        ];
    }

    public function removeDetalle($index)
    {
        unset($this->detalles[$index]);
        $this->detalles = array_values($this->detalles);
    }

    public function rules()
    {
        return [
            'numero_orden' => ['required', 'string', 'max:50', Rule::unique('ordenes_compra', 'numero_orden')],
            'proveedor_id' => ['required', 'exists:proveedores,id'],
            'sucursal_id' => ['required', 'exists:sucursales,id'],
            'fecha' => ['required', 'date'],
            'observacion' => ['nullable', 'string', 'max:1000'],
            'detalles' => ['required', 'array', 'min:1'],
            'detalles.*.catalogo_id' => ['required', 'exists:catalogos,id'],
            'detalles.*.cantidad' => ['required', 'integer', 'min:1'],
            'detalles.*.costo_unitario' => ['required', 'numeric', 'min:0'],
        ];
    }

    public function messages()
    {
        return [
            'numero_orden.required' => 'El número de orden es obligatorio.',
            'numero_orden.unique' => 'El número de orden ya existe.',
            'proveedor_id.required' => 'El proveedor es obligatorio.',
            'sucursal_id.required' => 'La sucursal es obligatoria.',
            'fecha.required' => 'La fecha es obligatoria.',
            'detalles.required' => 'Debe agregar al menos un producto.',
            'detalles.min' => 'Debe agregar al menos un producto.',
            'detalles.*.catalogo_id.required' => 'Seleccione un catálogo.',
            'detalles.*.cantidad.required' => 'La cantidad es obligatoria.',
            'detalles.*.cantidad.min' => 'La cantidad debe ser al menos 1.',
            'detalles.*.costo_unitario.required' => 'El costo unitario es obligatorio.',
        ];
    }

    public function save()
    {
        $this->validate();

        $orden = OrdenCompra::create([
            'numero_orden' => $this->numero_orden,
            'proveedor_id' => $this->proveedor_id,
            'sucursal_id' => $this->sucursal_id,
            'fecha' => $this->fecha,
            'observacion' => $this->observacion,
            'estado' => 'completada',
        ]);

        foreach ($this->detalles as $detalle) {
            $catalogo = Catalogo::find($detalle['catalogo_id']);

            if ($catalogo->serie_habilitada) {
                $seriesCodes = collect(preg_split('/[\r\n,]+/', $detalle['series']))
                    ->map(fn($s) => trim($s))
                    ->filter(fn($s) => $s !== '');

                if ($seriesCodes->isEmpty()) {
                    $this->addError("detalles.{$detalle['catalogo_id']}.series", 'Debe ingresar al menos un código de serie.');
                    return;
                }

                $producto = Producto::create([
                    'catalogo_id' => $detalle['catalogo_id'],
                    'orden_compra_id' => $orden->id,
                    'sucursal_id' => $orden->sucursal_id,
                    'cantidad' => $seriesCodes->count(),
                    'costo_unitario' => $detalle['costo_unitario'],
                ]);

                foreach ($seriesCodes as $codigo) {
                    Serie::create([
                        'producto_id' => $producto->id,
                        'catalogo_id' => $detalle['catalogo_id'],
                        'sucursal_id' => $orden->sucursal_id,
                        'codigo_serie' => $codigo,
                        'estado' => 'disponible',
                    ]);
                }
            } else {
                Producto::create([
                    'catalogo_id' => $detalle['catalogo_id'],
                    'orden_compra_id' => $orden->id,
                    'sucursal_id' => $orden->sucursal_id,
                    'cantidad' => $detalle['cantidad'],
                    'costo_unitario' => $detalle['costo_unitario'],
                ]);
            }
        }

        session()->flash('message', 'Orden de Compra creada correctamente');
        $this->redirect(route('ordenes-compra.index'), navigate: true);
    }

    public function with(): array
    {
        $catalogoIds = collect($this->detalles)->pluck('catalogo_id')->filter()->unique()->values()->toArray();
        $catalogosConSeries = Catalogo::whereIn('id', $catalogoIds)->where('serie_habilitada', true)->pluck('id')->toArray();

        return [
            'catalogos' => Catalogo::orderBy('nombre')->get(),
            'proveedores' => Proveedor::orderBy('nombre')->get(),
            'sucursales' => auth()->user()->sucursales,
            'catalogosConSeries' => $catalogosConSeries,
        ];
    }
}; ?>

<div>
    <x-slot name="header">
        <h2 class="text-xl font-semibold leading-tight text-gray-800">
            Crear Orden de Compra
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="mx-auto max-w-5xl sm:px-6 lg:px-8">
            <div class="overflow-hidden bg-white shadow-sm sm:rounded-lg">
                <div class="p-6">
                    <form wire:submit="save" class="space-y-6">
                        <div class="grid grid-cols-1 gap-6 md:grid-cols-2">
                            <div>
                                <label for="numero_orden" class="block text-sm font-medium text-gray-700">N° Orden</label>
                                <input
                                    id="numero_orden"
                                    type="text"
                                    wire:model="numero_orden"
                                    class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm"
                                >
                                @error('numero_orden') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
                            </div>

                            <div>
                                <label for="fecha" class="block text-sm font-medium text-gray-700">Fecha</label>
                                <input
                                    id="fecha"
                                    type="date"
                                    wire:model="fecha"
                                    class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm"
                                >
                                @error('fecha') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
                            </div>

                            <div>
                                <label for="proveedor_id" class="block text-sm font-medium text-gray-700">Proveedor</label>
                                <select
                                    id="proveedor_id"
                                    wire:model.live="proveedor_id"
                                    class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm"
                                >
                                    <option value="">Seleccione un proveedor</option>
                                    @foreach ($proveedores as $proveedor)
                                        <option value="{{ $proveedor->id }}">{{ $proveedor->nombre }}</option>
                                    @endforeach
                                </select>
                                @error('proveedor_id') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
                            </div>

                            <div>
                                <label for="sucursal_id" class="block text-sm font-medium text-gray-700">Sucursal</label>
                                <select
                                    id="sucursal_id"
                                    wire:model.live="sucursal_id"
                                    class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm"
                                >
                                    <option value="">Seleccione una sucursal</option>
                                    @foreach ($sucursales as $sucursal)
                                        <option value="{{ $sucursal->id }}">{{ $sucursal->nombre }}</option>
                                    @endforeach
                                </select>
                                @error('sucursal_id') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
                            </div>
                        </div>

                        <div>
                            <label for="observacion" class="block text-sm font-medium text-gray-700">Observación</label>
                            <textarea
                                id="observacion"
                                wire:model="observacion"
                                rows="3"
                                class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm"
                            ></textarea>
                            @error('observacion') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
                        </div>

                        <div>
                            <div class="flex items-center justify-between">
                                <h3 class="text-lg font-medium text-gray-900">Detalles</h3>
                                <button
                                    type="button"
                                    wire:click="addDetalle"
                                    class="inline-flex items-center rounded-md border border-transparent bg-indigo-600 px-3 py-1.5 text-sm font-medium text-white shadow-sm hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2"
                                >
                                    + Agregar Producto
                                </button>
                            </div>
                            @error('detalles') <p class="mt-2 text-sm text-red-600">{{ $message }}</p> @enderror

                            <div class="mt-4 space-y-4">
                                @foreach ($detalles as $index => $detalle)
                                    <div class="rounded-md border border-gray-200 p-4">
                                        <div class="flex items-start justify-between">
                                            <h4 class="text-sm font-medium text-gray-700">Producto #{{ $loop->iteration }}</h4>
                                            <button
                                                type="button"
                                                wire:click="removeDetalle({{ $index }})"
                                                class="text-sm font-medium text-red-600 hover:text-red-900"
                                            >
                                                Eliminar
                                            </button>
                                        </div>

                                        <div class="mt-3 grid grid-cols-1 gap-4 md:grid-cols-3">
                                            <div>
                                                <label class="block text-sm font-medium text-gray-700">Catálogo</label>
                                                <select
                                                    wire:model.live="detalles.{{ $index }}.catalogo_id"
                                                    class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm"
                                                >
                                                    <option value="">Seleccione</option>
                                                    @foreach ($catalogos as $catalogo)
                                                        <option value="{{ $catalogo->id }}">{{ $catalogo->codigo }} - {{ $catalogo->nombre }}</option>
                                                    @endforeach
                                                </select>
                                                @error("detalles.{$index}.catalogo_id") <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
                                            </div>

                                            <div>
                                                <label class="block text-sm font-medium text-gray-700">Cantidad</label>
                                                <input
                                                    type="number"
                                                    wire:model="detalles.{{ $index }}.cantidad"
                                                    min="1"
                                                    class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm"
                                                >
                                                @error("detalles.{$index}.cantidad") <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
                                            </div>

                                            <div>
                                                <label class="block text-sm font-medium text-gray-700">Costo Unitario</label>
                                                <input
                                                    type="number"
                                                    step="0.01"
                                                    wire:model="detalles.{{ $index }}.costo_unitario"
                                                    min="0"
                                                    class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm"
                                                >
                                                @error("detalles.{$index}.costo_unitario") <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
                                            </div>
                                        </div>

                                        @if (in_array($detalle['catalogo_id'], $catalogosConSeries))
                                            <div class="mt-3">
                                                <label class="block text-sm font-medium text-gray-700">Códigos de Serie</label>
                                                <textarea
                                                    wire:model="detalles.{{ $index }}.series"
                                                    rows="3"
                                                    class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm"
                                                    placeholder="Ingrese los códigos de serie, separados por coma o por línea"
                                                ></textarea>
                                                @error("detalles.{$index}.series") <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
                                            </div>
                                        @endif
                                    </div>
                                @endforeach
                            </div>

                            @if (empty($detalles))
                                <p class="mt-4 text-sm text-gray-500">No hay productos agregados. Haga clic en "Agregar Producto" para comenzar.</p>
                            @endif
                        </div>

                        <div class="flex gap-4">
                            <button
                                type="submit"
                                class="inline-flex items-center rounded-md border border-transparent bg-indigo-600 px-4 py-2 text-sm font-medium text-white shadow-sm hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2"
                            >
                                Guardar
                            </button>
                            <a
                                href="{{ route('ordenes-compra.index') }}"
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
