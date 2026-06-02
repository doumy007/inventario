<?php
use Livewire\Attributes\Layout;

use Livewire\Volt\Component;
use App\Models\Catalogo;
use App\Models\Serie;
use App\Models\Sucursal;
use App\Models\Venta;
use App\Models\VentaDetalle;
use App\Models\VentaSerie;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

new #[Layout('layouts.app')] class extends Component
{
    public $cliente_rut = '';
    public $cliente_nombre = '';
    public $sucursal_id;

    public $items = [];

    public $catalogo_search = '';
    public $catalogo_search_results = [];
    public $catalogo_id = null;
    public $selected_catalogo = null;
    public $cantidad = 1;

    public $serie_input = '';
    public $series_scanned = [];

    public function mount()
    {
        $user = Auth::user();
        $predeterminada = $user->sucursales()->wherePivot('predeterminada', true)->first();
        $this->sucursal_id = $predeterminada?->id ?? $user->sucursales()->first()?->id;
    }

    public function updatedCatalogoSearch($value)
    {
        if (strlen($value) < 1) {
            $this->catalogo_search_results = [];
            return;
        }

        $this->catalogo_search_results = Catalogo::where(function ($q) use ($value) {
            $q->where('codigo', 'like', "%{$value}%")
              ->orWhere('nombre', 'like', "%{$value}%");
        })
        ->limit(10)
        ->get()
        ->toArray();
    }

    public function selectCatalogo($id)
    {
        $catalogo = Catalogo::findOrFail($id);
        $this->catalogo_id = $catalogo->id;
        $this->selected_catalogo = $catalogo;
        $this->catalogo_search = $catalogo->codigo . ' - ' . $catalogo->nombre;
        $this->catalogo_search_results = [];
        $this->cantidad = 1;
        $this->series_scanned = [];
        $this->serie_input = '';
    }

    public function validarSerie($codigo)
    {
        if (!$this->catalogo_id) {
            return ['valida' => false, 'mensaje' => 'Selecciona un producto primero.'];
        }

        if (!$this->sucursal_id) {
            return ['valida' => false, 'mensaje' => 'No hay sucursal asignada.'];
        }

        if (in_array($codigo, $this->series_scanned)) {
            return ['valida' => false, 'mensaje' => 'Este código de serie ya fue escaneado.'];
        }

        $serie = Serie::where('codigo_serie', $codigo)
            ->where('catalogo_id', $this->catalogo_id)
            ->where('sucursal_id', $this->sucursal_id)
            ->where('estado', 'disponible')
            ->first();

        if (!$serie) {
            return ['valida' => false, 'mensaje' => 'El código de serie no es válido o no está disponible.'];
        }

        return ['valida' => true, 'serie' => $serie];
    }

    public function agregarSerie()
    {
        $codigo = trim($this->serie_input);
        if (empty($codigo)) return;

        $resultado = $this->validarSerie($codigo);

        if ($resultado['valida']) {
            $this->series_scanned[] = $codigo;
            $this->serie_input = '';
        } else {
            $this->dispatch('notify', type: 'error', message: $resultado['mensaje']);
            $this->serie_input = '';
        }
    }

    public function removerSerie($index)
    {
        unset($this->series_scanned[$index]);
        $this->series_scanned = array_values($this->series_scanned);
    }

    public function agregarItem()
    {
        if (!$this->selected_catalogo) {
            $this->dispatch('notify', type: 'error', message: 'Selecciona un producto.');
            return;
        }

        $catalogo = $this->selected_catalogo;

        if ($catalogo['serie_habilitada']) {
            if (count($this->series_scanned) !== (int) $this->cantidad) {
                $this->dispatch('notify', type: 'error', message: 'La cantidad de series escaneadas debe coincidir con la cantidad.');
                return;
            }
        }

        $precio = $catalogo['precio'];
        $subtotal = $precio * (int) $this->cantidad;

        $this->items[] = [
            'catalogo_id' => $catalogo['id'],
            'codigo' => $catalogo['codigo'],
            'nombre' => $catalogo['nombre'],
            'cantidad' => (int) $this->cantidad,
            'precio_unitario' => $precio,
            'subtotal' => $subtotal,
            'series' => $catalogo['serie_habilitada'] ? $this->series_scanned : [],
        ];

        $this->resetSelection();
    }

    public function eliminarItem($index)
    {
        unset($this->items[$index]);
        $this->items = array_values($this->items);
    }

    public function finalizar()
    {
        if (empty($this->items)) {
            $this->dispatch('notify', type: 'error', message: 'Agrega al menos un producto a la venta.');
            return;
        }

        if (!$this->sucursal_id) {
            $this->dispatch('notify', type: 'error', message: 'No hay sucursal asignada.');
            return;
        }

        DB::transaction(function () {
            $fecha = now();
            $folio = 'V-' . $fecha->format('Ymd') . '-' . str_pad(Venta::whereDate('created_at', $fecha->toDateString())->count() + 1, 4, '0', STR_PAD_LEFT);

            $total = array_sum(array_column($this->items, 'subtotal'));

            $venta = Venta::create([
                'folio' => $folio,
                'cliente_rut' => $this->cliente_rut ?: null,
                'cliente_nombre' => $this->cliente_nombre ?: null,
                'subtotal' => $total,
                'total' => $total,
                'user_id' => Auth::id(),
                'sucursal_id' => $this->sucursal_id,
            ]);

            foreach ($this->items as $item) {
                $detalle = VentaDetalle::create([
                    'venta_id' => $venta->id,
                    'catalogo_id' => $item['catalogo_id'],
                    'cantidad' => $item['cantidad'],
                    'precio_unitario' => $item['precio_unitario'],
                    'subtotal' => $item['subtotal'],
                ]);

                foreach ($item['series'] as $codigoSerie) {
                    $serie = Serie::where('codigo_serie', $codigoSerie)
                        ->where('estado', 'disponible')
                        ->lockForUpdate()
                        ->first();

                    if ($serie) {
                        VentaSerie::create([
                            'venta_detalle_id' => $detalle->id,
                            'serie_id' => $serie->id,
                        ]);
                        $serie->update(['estado' => 'vendido']);
                    }
                }
            }

            session()->flash('success', "Venta {$folio} registrada correctamente.");
            $this->redirect(route('ventas.show', $venta->id), navigate: true);
        });
    }

    public function getTotalProperty()
    {
        return array_sum(array_column($this->items, 'subtotal'));
    }

    public function getSucursalesProperty()
    {
        return Auth::user()->sucursales;
    }

    private function resetSelection()
    {
        $this->catalogo_search = '';
        $this->catalogo_search_results = [];
        $this->catalogo_id = null;
        $this->selected_catalogo = null;
        $this->cantidad = 1;
        $this->series_scanned = [];
        $this->serie_input = '';
    }
}; ?>

<div>
    <x-slot name="header">
        <h2 class="text-xl font-semibold leading-tight text-gray-800">
            Nueva Venta (POS)
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="mx-auto max-w-7xl sm:px-6 lg:px-8">
            <div class="grid grid-cols-1 gap-6 lg:grid-cols-3">
                {{-- Left: Datos del cliente y sucursal --}}
                <div class="lg:col-span-2 space-y-6">
                    <div class="overflow-hidden bg-white shadow-sm sm:rounded-lg">
                        <div class="p-6">
                            <h3 class="mb-4 text-lg font-medium text-gray-900">Datos de la Venta</h3>
                            <div class="grid grid-cols-1 gap-4 sm:grid-cols-3">
                                <div>
                                    <label class="block text-sm font-medium text-gray-700">Sucursal</label>
                                    <select wire:model="sucursal_id"
                                        class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm">
                                        <option value="">Seleccionar sucursal</option>
                                        @foreach ($this->sucursales as $sucursal)
                                            <option value="{{ $sucursal->id }}">{{ $sucursal->nombre }}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <div>
                                    <label class="block text-sm font-medium text-gray-700">Cliente RUT</label>
                                    <input type="text" wire:model.live="cliente_rut" placeholder="Opcional"
                                        class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm">
                                </div>
                                <div>
                                    <label class="block text-sm font-medium text-gray-700">Cliente Nombre</label>
                                    <input type="text" wire:model.live="cliente_nombre" placeholder="Opcional"
                                        class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm">
                                </div>
                            </div>
                        </div>
                    </div>

                    {{-- Product selection --}}
                    <div class="overflow-hidden bg-white shadow-sm sm:rounded-lg">
                        <div class="p-6">
                            <h3 class="mb-4 text-lg font-medium text-gray-900">Agregar Producto</h3>

                            <div class="relative mb-4">
                                <label class="block text-sm font-medium text-gray-700">Buscar Producto</label>
                                <input type="text" wire:model.live="catalogo_search" placeholder="Buscar por código o nombre..."
                                    class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm">
                                @if (count($catalogo_search_results) > 0)
                                    <div class="absolute z-10 mt-1 w-full rounded-md border border-gray-200 bg-white shadow-lg">
                                        @foreach ($catalogo_search_results as $result)
                                            <div wire:click="selectCatalogo({{ $result['id'] }})"
                                                class="cursor-pointer px-4 py-2 text-sm hover:bg-indigo-50">
                                                <span class="font-medium">{{ $result['codigo'] }}</span> - {{ $result['nombre'] }}
                                                <span class="text-gray-500">(${{ number_format($result['precio']) }})</span>
                                                <span class="text-xs text-gray-400">Stock: {{ $result['stock'] ?? '-' }}</span>
                                            </div>
                                        @endforeach
                                    </div>
                                @endif
                            </div>

                            @if ($selected_catalogo)
                                <div class="mb-4 rounded-md bg-gray-50 p-4">
                                    <p class="text-sm"><span class="font-medium">Seleccionado:</span> {{ $selected_catalogo['codigo'] }} - {{ $selected_catalogo['nombre'] }}</p>
                                    <p class="text-sm"><span class="font-medium">Precio:</span> ${{ number_format($selected_catalogo['precio']) }}</p>
                                    <p class="text-sm"><span class="font-medium">Control por serie:</span> {{ $selected_catalogo['serie_habilitada'] ? 'Sí' : 'No' }}</p>
                                </div>

                                <div class="grid grid-cols-1 gap-4 sm:grid-cols-2">
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700">Cantidad</label>
                                        <input type="number" wire:model.live="cantidad" min="1"
                                            class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm">
                                    </div>
                                </div>

                                @if ($selected_catalogo['serie_habilitada'])
                                    <div class="mt-4 rounded-md border border-yellow-200 bg-yellow-50 p-4">
                                        <label class="block text-sm font-medium text-gray-700">Escanear Series</label>
                                        <p class="mb-2 text-xs text-gray-500">Escanea o ingresa los códigos de serie uno por uno.</p>
                                        <div class="flex gap-2">
                                            <input type="text" wire:model.live="serie_input" wire:keydown.enter="agregarSerie" placeholder="Código de serie..."
                                                class="block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm">
                                            <button wire:click="agregarSerie"
                                                class="inline-flex items-center rounded-md bg-indigo-600 px-3 py-2 text-sm font-medium text-white hover:bg-indigo-700">
                                                Agregar
                                            </button>
                                        </div>

                                        @if (count($series_scanned) > 0)
                                            <div class="mt-3">
                                                <p class="text-sm font-medium text-gray-700">
                                                    Series escaneadas: <span class="text-indigo-600">{{ count($series_scanned) }}</span> / {{ $cantidad }}
                                                </p>
                                                <ul class="mt-2 space-y-1">
                                                    @foreach ($series_scanned as $index => $serie)
                                                        <li class="flex items-center justify-between text-sm">
                                                            <span class="text-green-700">{{ $serie }}</span>
                                                            <button wire:click="removerSerie({{ $index }})" class="text-red-500 hover:text-red-700">&times;</button>
                                                        </li>
                                                    @endforeach
                                                </ul>
                                            </div>
                                        @endif
                                    </div>
                                @endif

                                <div class="mt-4">
                                    <button wire:click="agregarItem"
                                        class="inline-flex items-center rounded-md bg-green-600 px-4 py-2 text-sm font-medium text-white shadow-sm hover:bg-green-700 focus:outline-none focus:ring-2 focus:ring-green-500 focus:ring-offset-2">
                                        + Agregar a la Venta
                                    </button>
                                </div>
                            @endif
                        </div>
                    </div>
                </div>

                {{-- Right: Resumen del carrito --}}
                <div class="space-y-6">
                    <div class="overflow-hidden bg-white shadow-sm sm:rounded-lg">
                        <div class="p-6">
                            <h3 class="mb-4 text-lg font-medium text-gray-900">Resumen de Venta</h3>

                            @if (count($items) > 0)
                                <div class="space-y-3">
                                    @foreach ($items as $index => $item)
                                        <div class="rounded-md border border-gray-200 bg-gray-50 p-3 text-sm">
                                            <div class="flex items-start justify-between">
                                                <div>
                                                    <p class="font-medium">{{ $item['codigo'] }} - {{ $item['nombre'] }}</p>
                                                    <p class="text-gray-600">
                                                        Precio: ${{ number_format($item['precio_unitario']) }} |
                                                        Cant: {{ $item['cantidad'] }} |
                                                        Total: ${{ number_format($item['subtotal']) }}
                                                    </p>
                                                    @if (count($item['series']) > 0)
                                                        <p class="mt-1 text-xs text-gray-500">
                                                            Series: {{ implode(', ', $item['series']) }}
                                                        </p>
                                                    @endif
                                                </div>
                                                <button wire:click="eliminarItem({{ $index }})"
                                                    class="text-red-500 hover:text-red-700">&times;</button>
                                            </div>
                                        </div>
                                    @endforeach
                                </div>

                                <div class="mt-4 border-t border-gray-200 pt-4">
                                    <p class="text-xl font-bold text-gray-900">
                                        Total: ${{ number_format($this->total) }}
                                    </p>
                                </div>

                                <div class="mt-6">
                                    <button wire:click="finalizar" wire:confirm="¿Confirmar la venta?"
                                        class="w-full inline-flex items-center justify-center rounded-md bg-indigo-600 px-4 py-3 text-sm font-medium text-white shadow-sm hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2">
                                        Finalizar Venta
                                    </button>
                                </div>
                            @else
                                <p class="text-sm text-gray-500">No hay productos agregados.</p>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    @script
    <script>
        $wire.on('notify', (data) => {
            alert(data.message);
        });
    </script>
    @endscript
</div>


