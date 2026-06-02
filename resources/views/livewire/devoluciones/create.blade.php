<?php
use Livewire\Attributes\Layout;

use Livewire\Volt\Component;
use App\Models\Venta;
use App\Models\VentaDetalle;
use App\Models\Devolucion;
use App\Models\DevolucionDetalle;
use App\Models\DevolucionSerie;
use App\Models\Serie;
use App\Models\OrdenIngreso;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;

new #[Layout('layouts.app')] class extends Component
{
    public string $search = '';
    public ?int $venta_id = null;
    public ?Venta $venta = null;
    public string $motivo = '';
    public array $cantidades = [];
    public array $series = [];

    public function rules()
    {
        return [
            'venta_id' => ['required', 'exists:ventas,id'],
            'motivo' => ['required', 'string', 'max:1000'],
        ];
    }

    public function messages()
    {
        return [
            'venta_id.required' => 'Debe seleccionar una venta.',
            'venta_id.exists' => 'La venta seleccionada no existe.',
            'motivo.required' => 'El motivo es obligatorio.',
        ];
    }

    public function searchVenta()
    {
        $venta = Venta::where('folio', $this->search)->first();

        if (!$venta) {
            session()->flash('error', 'Venta no encontrada.');
            return;
        }

        $this->venta = $venta;
        $this->venta_id = $venta->id;

        $this->cantidades = [];
        $this->series = [];

        foreach ($venta->detalles as $detalle) {
            $this->cantidades[$detalle->id] = 0;
            if ($detalle->catalogo->serie_habilitada) {
                $this->series[$detalle->id] = '';
            }
        }
    }

    public function selectVenta($id)
    {
        $this->venta = Venta::find($id);
        $this->venta_id = (int) $id;
        $this->search = $this->venta->folio;

        $this->cantidades = [];
        $this->series = [];

        foreach ($this->venta->detalles as $detalle) {
            $this->cantidades[$detalle->id] = 0;
            if ($detalle->catalogo->serie_habilitada) {
                $this->series[$detalle->id] = '';
            }
        }
    }

    public function generar()
    {
        $this->validate();

        if (!$this->venta) {
            session()->flash('error', 'Debe seleccionar una venta.');
            return;
        }

        DB::beginTransaction();
        try {
            $devolucion = Devolucion::create([
                'folio' => $this->generarFolio(),
                'venta_id' => $this->venta->id,
                'motivo' => $this->motivo,
                'user_id' => auth()->id(),
                'sucursal_id' => $this->venta->sucursal_id,
            ]);

            foreach ($this->venta->detalles as $detalle) {
                $cantidad = (int) ($this->cantidades[$detalle->id] ?? 0);

                if ($cantidad <= 0) {
                    continue;
                }

                if ($cantidad > $detalle->cantidad) {
                    throw new \Exception("Cantidad a devolver excede la cantidad original para {$detalle->catalogo->nombre}.");
                }

                $subtotal = $cantidad * $detalle->precio_unitario;

                $devDetalle = DevolucionDetalle::create([
                    'devolucion_id' => $devolucion->id,
                    'catalogo_id' => $detalle->catalogo_id,
                    'cantidad' => $cantidad,
                    'precio_unitario' => $detalle->precio_unitario,
                    'subtotal' => $subtotal,
                ]);

                if ($detalle->catalogo->serie_habilitada && !empty($this->series[$detalle->id])) {
                    $codigos = explode("\n", trim($this->series[$detalle->id]));
                    $codigos = array_map('trim', $codigos);
                    $codigos = array_filter($codigos);

                    foreach ($codigos as $codigo) {
                        $serie = Serie::where('codigo_serie', $codigo)
                            ->where('catalogo_id', $detalle->catalogo_id)
                            ->first();

                        if (!$serie) {
                            throw new \Exception("Serie {$codigo} no encontrada.");
                        }

                        $ventaSerie = $serie->ventaSeries()
                            ->whereHas('ventaDetalle', function ($q) {
                                $q->where('venta_id', $this->venta->id);
                            })
                            ->first();

                        if (!$ventaSerie || $serie->estado !== 'vendido') {
                            throw new \Exception("La serie {$codigo} no pertenece a esta venta o no está vendida.");
                        }

                        DevolucionSerie::create([
                            'devolucion_detalle_id' => $devDetalle->id,
                            'serie_id' => $serie->id,
                        ]);

                        $serie->update(['estado' => 'disponible']);
                    }
                }
            }

            OrdenIngreso::create([
                'numero_ingreso' => 'ING-D-' . $devolucion->folio,
                'devolucion_id' => $devolucion->id,
                'sucursal_id' => $this->venta->sucursal_id,
                'observacion' => "Ingreso por devolución {$devolucion->folio}",
            ]);

            DB::commit();

            session()->flash('message', "Devolución {$devolucion->folio} generada correctamente.");
            $this->redirect(route('devoluciones.index'), navigate: true);
        } catch (\Exception $e) {
            DB::rollBack();
            session()->flash('error', $e->getMessage());
        }
    }

    private function generarFolio(): string
    {
        $prefix = 'D-' . now()->format('Ymd') . '-';
        $last = Devolucion::where('folio', 'like', $prefix . '%')
            ->orderBy('folio', 'desc')
            ->first();

        if ($last) {
            $num = (int) substr($last->folio, strlen($prefix)) + 1;
        } else {
            $num = 1;
        }

        return $prefix . str_pad($num, 4, '0', STR_PAD_LEFT);
    }
}; ?>

<div>
    <x-slot name="header">
        <h2 class="text-xl font-semibold leading-tight text-gray-800">
            Nueva Devolución
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="mx-auto max-w-4xl sm:px-6 lg:px-8">
            @if (session('error'))
                <div class="mb-4 rounded-md bg-red-50 p-4 text-sm text-red-800">
                    {{ session('error') }}
                </div>
            @endif

            @if (session('message'))
                <div class="mb-4 rounded-md bg-green-50 p-4 text-sm text-green-800">
                    {{ session('message') }}
                </div>
            @endif

            <div class="overflow-hidden bg-white shadow-sm sm:rounded-lg">
                <div class="p-6">
                    <div class="mb-6">
                        <label class="block text-sm font-medium text-gray-700">Buscar Venta por Folio</label>
                        <div class="mt-1 flex gap-2">
                            <input
                                type="text"
                                wire:model="search"
                                wire:keydown.enter="searchVenta"
                                class="block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm"
                                placeholder="Ingrese folio de la venta..."
                            >
                            <button
                                type="button"
                                wire:click="searchVenta"
                                class="inline-flex items-center rounded-md border border-transparent bg-indigo-600 px-4 py-2 text-sm font-medium text-white shadow-sm hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2"
                            >
                                Buscar
                            </button>
                        </div>
                    </div>

                    @if ($venta)
                        <div class="mb-6 rounded-md bg-gray-50 p-4">
                            <h3 class="font-semibold text-gray-800">Venta: {{ $venta->folio }}</h3>
                            <p class="text-sm text-gray-600">Cliente: {{ $venta->cliente_nombre }} ({{ $venta->cliente_rut }})</p>
                            <p class="text-sm text-gray-600">Fecha: {{ $venta->created_at->format('d/m/Y H:i') }}</p>
                            <p class="text-sm text-gray-600">Total: ${{ number_format($venta->total) }}</p>
                        </div>

                        <form wire:submit="generar" class="space-y-6">
                            <div class="overflow-x-auto border-b border-gray-200">
                                <table class="min-w-full divide-y divide-gray-200 text-sm">
                                    <thead class="bg-gray-50">
                                        <tr>
                                            <th class="px-4 py-3 text-left font-medium text-gray-500 uppercase tracking-wider">Producto</th>
                                            <th class="px-4 py-3 text-center font-medium text-gray-500 uppercase tracking-wider">Cant. Original</th>
                                            <th class="px-4 py-3 text-center font-medium text-gray-500 uppercase tracking-wider">A Devolver</th>
                                            @if ($venta->detalles->first(fn($d) => $d->catalogo->serie_habilitada))
                                                <th class="px-4 py-3 text-left font-medium text-gray-500 uppercase tracking-wider">Series</th>
                                            @endif
                                        </tr>
                                    </thead>
                                    <tbody class="divide-y divide-gray-200 bg-white">
                                        @foreach ($venta->detalles as $detalle)
                                            <tr>
                                                <td class="px-4 py-3">
                                                    {{ $detalle->catalogo->codigo }} - {{ $detalle->catalogo->nombre }}
                                                    <br>
                                                    <span class="text-xs text-gray-500">${{ number_format($detalle->precio_unitario) }} c/u</span>
                                                </td>
                                                <td class="px-4 py-3 text-center">{{ $detalle->cantidad }}</td>
                                                <td class="px-4 py-3 text-center">
                                                    <input
                                                        type="number"
                                                        wire:model="cantidades.{{ $detalle->id }}"
                                                        min="0"
                                                        max="{{ $detalle->cantidad }}"
                                                        class="w-20 rounded-md border-gray-300 text-center shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm"
                                                    >
                                                    @error("cantidades.{$detalle->id}") <p class="text-xs text-red-600">{{ $message }}</p> @enderror
                                                </td>
                                                @if ($detalle->catalogo->serie_habilitada)
                                                    <td class="px-4 py-3">
                                                        <textarea
                                                            wire:model="series.{{ $detalle->id }}"
                                                            rows="2"
                                                            class="block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-xs"
                                                            placeholder="Ingrese códigos de serie (uno por línea)"
                                                        ></textarea>
                                                    </td>
                                                @endif
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>

                            <div>
                                <label for="motivo" class="block text-sm font-medium text-gray-700">Motivo de la Devolución</label>
                                <textarea
                                    id="motivo"
                                    wire:model="motivo"
                                    rows="3"
                                    class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm"
                                ></textarea>
                                @error('motivo') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
                            </div>

                            <div class="flex gap-4">
                                <button
                                    type="submit"
                                    class="inline-flex items-center rounded-md border border-transparent bg-indigo-600 px-4 py-2 text-sm font-medium text-white shadow-sm hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2"
                                >
                                    Generar Devolución
                                </button>
                                <a
                                    href="{{ route('devoluciones.index') }}"
                                    navigate
                                    class="inline-flex items-center rounded-md border border-gray-300 bg-white px-4 py-2 text-sm font-medium text-gray-700 shadow-sm hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2"
                                >
                                    Cancelar
                                </a>
                            </div>
                        </form>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>


