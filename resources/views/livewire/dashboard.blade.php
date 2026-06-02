<?php
use Livewire\Attributes\Layout;

use App\Models\Producto;
use App\Models\Venta;
use App\Models\Catalogo;
use App\Models\User;
use Livewire\Volt\Component;

new #[Layout('layouts.app')] class extends Component
{
    public int $ventasHoy = 0;
    public int $totalStock = 0;
    public int $totalCatalogos = 0;
    public int $totalUsuarios = 0;

    public function mount(): void
    {
        $this->ventasHoy = Venta::whereDate('created_at', today())->count();
        $this->totalStock = Producto::sum('cantidad');
        $this->totalCatalogos = Catalogo::count();
        $this->totalUsuarios = User::count();
    }
}; ?>

<div>
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
        <div class="bg-white rounded-lg shadow-md p-6 border-l-4 border-blue-500">
            <p class="text-sm font-medium text-gray-500 uppercase tracking-wider">Total de Ventas Hoy</p>
            <p class="mt-2 text-3xl font-bold text-gray-900">{{ number_format($ventasHoy) }}</p>
        </div>

        <div class="bg-white rounded-lg shadow-md p-6 border-l-4 border-green-500">
            <p class="text-sm font-medium text-gray-500 uppercase tracking-wider">Total de Productos en Stock</p>
            <p class="mt-2 text-3xl font-bold text-gray-900">{{ number_format($totalStock) }}</p>
        </div>

        <div class="bg-white rounded-lg shadow-md p-6 border-l-4 border-purple-500">
            <p class="text-sm font-medium text-gray-500 uppercase tracking-wider">Total de Catálogos</p>
            <p class="mt-2 text-3xl font-bold text-gray-900">{{ number_format($totalCatalogos) }}</p>
        </div>

        <div class="bg-white rounded-lg shadow-md p-6 border-l-4 border-yellow-500">
            <p class="text-sm font-medium text-gray-500 uppercase tracking-wider">Total de Usuarios</p>
            <p class="mt-2 text-3xl font-bold text-gray-900">{{ number_format($totalUsuarios) }}</p>
        </div>
    </div>
</div>


