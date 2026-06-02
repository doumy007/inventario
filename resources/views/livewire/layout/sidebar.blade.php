<?php
use Livewire\Volt\Component;

new class extends Component
{
    public function logout(): void
    {
        Auth::guard('web')->logout();
        session()->invalidate();
        session()->regenerateToken();
        $this->redirect('/', navigate: true);
    }
};
?>

<nav class="space-y-1 px-3 py-4">
    <a href="{{ route('dashboard') }}" wire:navigate class="group flex items-center px-3 py-2 text-sm font-medium rounded-md {{ request()->routeIs('dashboard') ? 'bg-indigo-50 text-indigo-700' : 'text-gray-700 hover:text-gray-900 hover:bg-gray-50' }}">
        <svg class="mr-3 h-5 w-5 flex-shrink-0 {{ request()->routeIs('dashboard') ? 'text-indigo-500' : 'text-gray-400 group-hover:text-gray-500' }}" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M3.75 6A2.25 2.25 0 016 3.75h2.25A2.25 2.25 0 0110.5 6v2.25a2.25 2.25 0 01-2.25 2.25H6a2.25 2.25 0 01-2.25-2.25V6zM3.75 15.75A2.25 2.25 0 016 13.5h2.25a2.25 2.25 0 012.25 2.25V18a2.25 2.25 0 01-2.25 2.25H6A2.25 2.25 0 013.75 18v-2.25zM13.5 6a2.25 2.25 0 012.25-2.25H18A2.25 2.25 0 0120.25 6v2.25A2.25 2.25 0 0118 10.5h-2.25a2.25 2.25 0 01-2.25-2.25V6zM13.5 15.75a2.25 2.25 0 012.25-2.25H18a2.25 2.25 0 012.25 2.25V18A2.25 2.25 0 0118 20.25h-2.25A2.25 2.25 0 0113.5 18v-2.25z" /></svg>
        Dashboard
    </a>

    @can('sucursales-ver')
    <div class="pt-4">
        <p class="px-3 text-xs font-semibold text-gray-500 uppercase tracking-wider">Configuración</p>
    </div>
    <a href="{{ route('sucursales.index') }}" wire:navigate class="group flex items-center px-3 py-2 text-sm font-medium rounded-md {{ request()->routeIs('sucursales*') ? 'bg-indigo-50 text-indigo-700' : 'text-gray-700 hover:text-gray-900 hover:bg-gray-50' }}">
        <svg class="mr-3 h-5 w-5 flex-shrink-0 {{ request()->routeIs('sucursales*') ? 'text-indigo-500' : 'text-gray-400 group-hover:text-gray-500' }}" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M3.75 21h16.5M4.5 3h15M5.25 3v18m13.5-18v18M9 6.75h1.5m-1.5 3h1.5m-1.5 3h1.5m3-6H15m-1.5 3H15m-1.5 3H15M9 21v-3.375c0-.621.504-1.125 1.125-1.125h3.75c.621 0 1.125.504 1.125 1.125V21" /></svg>
        Sucursales
    </a>
    @endcan

    @can('usuarios-ver')
    <a href="{{ route('usuarios.index') }}" wire:navigate class="group flex items-center px-3 py-2 text-sm font-medium rounded-md {{ request()->routeIs('usuarios*') ? 'bg-indigo-50 text-indigo-700' : 'text-gray-700 hover:text-gray-900 hover:bg-gray-50' }}">
        <svg class="mr-3 h-5 w-5 flex-shrink-0 {{ request()->routeIs('usuarios*') ? 'text-indigo-500' : 'text-gray-400 group-hover:text-gray-500' }}" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M15 19.128a9.38 9.38 0 002.625.372 9.337 9.337 0 004.121-.952 4.125 4.125 0 00-7.533-2.493M15 19.128v-.003c0-1.113-.285-2.16-.786-3.07M15 19.128v.106A12.318 12.318 0 018.624 21c-2.331 0-4.512-.645-6.374-1.766l-.001-.109a6.375 6.375 0 0111.964-3.07M12 6.375a3.375 3.375 0 11-6.75 0 3.375 3.375 0 016.75 0zm8.25 2.25a2.625 2.625 0 11-5.25 0 2.625 2.625 0 015.25 0z" /></svg>
        Usuarios
    </a>
    @endcan

    @can('roles-ver')
    <a href="{{ route('roles.index') }}" wire:navigate class="group flex items-center px-3 py-2 text-sm font-medium rounded-md {{ request()->routeIs('roles*') ? 'bg-indigo-50 text-indigo-700' : 'text-gray-700 hover:text-gray-900 hover:bg-gray-50' }}">
        <svg class="mr-3 h-5 w-5 flex-shrink-0 {{ request()->routeIs('roles*') ? 'text-indigo-500' : 'text-gray-400 group-hover:text-gray-500' }}" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75L11.25 15 15 9.75m-3-7.036A11.959 11.959 0 013.598 6 11.99 11.99 0 003 9.749c0 5.592 3.824 10.29 9 11.623 5.176-1.332 9-6.03 9-11.622 0-1.31-.21-2.571-.598-3.751h-.152c-3.196 0-6.1-1.248-8.25-3.285z" /></svg>
        Roles y Permisos
    </a>
    @endcan

    <div class="pt-4">
        <p class="px-3 text-xs font-semibold text-gray-500 uppercase tracking-wider">Productos</p>
    </div>
    @can('catalogos-ver')
    <a href="{{ route('catalogos.index') }}" wire:navigate class="group flex items-center px-3 py-2 text-sm font-medium rounded-md {{ request()->routeIs('catalogos*') ? 'bg-indigo-50 text-indigo-700' : 'text-gray-700 hover:text-gray-900 hover:bg-gray-50' }}">
        <svg class="mr-3 h-5 w-5 flex-shrink-0 {{ request()->routeIs('catalogos*') ? 'text-indigo-500' : 'text-gray-400 group-hover:text-gray-500' }}" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M20.25 7.5l-.625 10.632a2.25 2.25 0 01-2.247 2.118H6.622a2.25 2.25 0 01-2.247-2.118L3.75 7.5M10 11.25h4M3.375 7.5h17.25c.621 0 1.125-.504 1.125-1.125v-1.5c0-.621-.504-1.125-1.125-1.125H3.375c-.621 0-1.125.504-1.125 1.125v1.5c0 .621.504 1.125 1.125 1.125z" /></svg>
        Catálogo
    </a>
    @endcan

    @can('proveedores-ver')
    <a href="{{ route('proveedores.index') }}" wire:navigate class="group flex items-center px-3 py-2 text-sm font-medium rounded-md {{ request()->routeIs('proveedores*') ? 'bg-indigo-50 text-indigo-700' : 'text-gray-700 hover:text-gray-900 hover:bg-gray-50' }}">
        <svg class="mr-3 h-5 w-5 flex-shrink-0 {{ request()->routeIs('proveedores*') ? 'text-indigo-500' : 'text-gray-400 group-hover:text-gray-500' }}" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M13.5 21v-7.5a.75.75 0 01.75-.75h3a.75.75 0 01.75.75V21m-4.5 0H2.36m11.14 0H18m0 0h3.64m-1.39 0V9.349m-16.5 11.65V9.35m0 0a3.001 3.001 0 003.75-.615A2.993 2.993 0 009.75 9.75c.896 0 1.7-.393 2.25-1.016a2.993 2.993 0 002.25 1.016c.896 0 1.7-.393 2.25-1.016a3.001 3.001 0 003.75.614m-16.5 0a3.004 3.004 0 01-.621-4.72L4.318 3.44A1.5 1.5 0 015.378 3h13.243a1.5 1.5 0 011.06.44l1.19 1.189a3 3 0 01-.621 4.72m-13.5 8.65h3.75a.75.75 0 00.75-.75V13.5a.75.75 0 00-.75-.75H6.75a.75.75 0 00-.75.75v3.75c0 .415.336.75.75.75z" /></svg>
        Proveedores
    </a>
    @endcan

    @can('compras-ver')
    <a href="{{ route('ordenes-compra.index') }}" wire:navigate class="group flex items-center px-3 py-2 text-sm font-medium rounded-md {{ request()->routeIs('ordenes-compra*') ? 'bg-indigo-50 text-indigo-700' : 'text-gray-700 hover:text-gray-900 hover:bg-gray-50' }}">
        <svg class="mr-3 h-5 w-5 flex-shrink-0 {{ request()->routeIs('ordenes-compra*') ? 'text-indigo-500' : 'text-gray-400 group-hover:text-gray-500' }}" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M3.75 3v11.25A2.25 2.25 0 006 16.5h2.25M3.75 3h-1.5m1.5 0h16.5m0 0h1.5m-1.5 0v11.25A2.25 2.25 0 0118 16.5h-2.25m-7.5 0h7.5m-7.5 0l-1 3m8.5-3l1 3m0 0l.5 1.5m-.5-1.5h-9.5m0 0l-.5 1.5m.75-9l3-3 2.148 2.148A12.061 12.061 0 0116.5 7.605" /></svg>
        Órdenes de Compra
    </a>
    @endcan

    <div class="pt-4">
        <p class="px-3 text-xs font-semibold text-gray-500 uppercase tracking-wider">Ventas</p>
    </div>
    @can('ventas-crear')
    <a href="{{ route('ventas.create') }}" wire:navigate class="group flex items-center px-3 py-2 text-sm font-medium rounded-md {{ request()->routeIs('ventas.create') ? 'bg-indigo-50 text-indigo-700' : 'text-gray-700 hover:text-gray-900 hover:bg-gray-50' }}">
        <svg class="mr-3 h-5 w-5 flex-shrink-0 {{ request()->routeIs('ventas.create') ? 'text-indigo-500' : 'text-gray-400 group-hover:text-gray-500' }}" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M12 9v6m3-3H9m12 0a9 9 0 11-18 0 9 9 0 0118 0z" /></svg>
        Nueva Venta
    </a>
    @endcan

    @can('ventas-ver')
    <a href="{{ route('ventas.index') }}" wire:navigate class="group flex items-center px-3 py-2 text-sm font-medium rounded-md {{ request()->routeIs('ventas.index') || request()->routeIs('ventas.show*') ? 'bg-indigo-50 text-indigo-700' : 'text-gray-700 hover:text-gray-900 hover:bg-gray-50' }}">
        <svg class="mr-3 h-5 w-5 flex-shrink-0 {{ request()->routeIs('ventas.index') || request()->routeIs('ventas.show*') ? 'text-indigo-500' : 'text-gray-400 group-hover:text-gray-500' }}" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M2.25 8.25h19.5M2.25 9h19.5m-16.5 5.25h6m-6 2.25h3m-3.75 3h15a2.25 2.25 0 002.25-2.25V6.75A2.25 2.25 0 0019.5 4.5h-15a2.25 2.25 0 00-2.25 2.25v10.5A2.25 2.25 0 004.5 19.5z" /></svg>
        Historial Ventas
    </a>
    @endcan

    @can('devoluciones-ver')
    <a href="{{ route('devoluciones.create') }}" wire:navigate class="group flex items-center px-3 py-2 text-sm font-medium rounded-md {{ request()->routeIs('devoluciones*') ? 'bg-indigo-50 text-indigo-700' : 'text-gray-700 hover:text-gray-900 hover:bg-gray-50' }}">
        <svg class="mr-3 h-5 w-5 flex-shrink-0 {{ request()->routeIs('devoluciones*') ? 'text-indigo-500' : 'text-gray-400 group-hover:text-gray-500' }}" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M9 15L3 9m0 0l6-6M3 9h12a6 6 0 010 12h-3" /></svg>
        Devoluciones
    </a>
    @endcan

    <div class="pt-4">
        <p class="px-3 text-xs font-semibold text-gray-500 uppercase tracking-wider">Inventario</p>
    </div>
    @can('inventario-ver')
    <a href="{{ route('inventario.stock') }}" wire:navigate class="group flex items-center px-3 py-2 text-sm font-medium rounded-md {{ request()->routeIs('inventario*') ? 'bg-indigo-50 text-indigo-700' : 'text-gray-700 hover:text-gray-900 hover:bg-gray-50' }}">
        <svg class="mr-3 h-5 w-5 flex-shrink-0 {{ request()->routeIs('inventario*') ? 'text-indigo-500' : 'text-gray-400 group-hover:text-gray-500' }}" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M20.25 7.5l-.625 10.632a2.25 2.25 0 01-2.247 2.118H6.622a2.25 2.25 0 01-2.247-2.118L3.75 7.5m8.25 3v6.75m0 0l-3-3m3 3l3-3M3.375 7.5h17.25c.621 0 1.125-.504 1.125-1.125v-1.5c0-.621-.504-1.125-1.125-1.125H3.375c-.621 0-1.125.504-1.125 1.125v1.5c0 .621.504 1.125 1.125 1.125z" /></svg>
        Stock Actual
    </a>
    @endcan

    @can('reportes-ver')
    <a href="{{ route('reportes.ventas') }}" wire:navigate class="group flex items-center px-3 py-2 text-sm font-medium rounded-md {{ request()->routeIs('reportes*') ? 'bg-indigo-50 text-indigo-700' : 'text-gray-700 hover:text-gray-900 hover:bg-gray-50' }}">
        <svg class="mr-3 h-5 w-5 flex-shrink-0 {{ request()->routeIs('reportes*') ? 'text-indigo-500' : 'text-gray-400 group-hover:text-gray-500' }}" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M19.5 14.25v-2.625a3.375 3.375 0 00-3.375-3.375h-1.5A1.125 1.125 0 0113.5 7.125v-1.5a3.375 3.375 0 00-3.375-3.375H8.25m0 12.75h7.5m-7.5 3H12M10.5 2.25H5.625c-.621 0-1.125.504-1.125 1.125v17.25c0 .621.504 1.125 1.125 1.125h12.75c.621 0 1.125-.504 1.125-1.125V11.25a9 9 0 00-9-9z" /></svg>
        Reportes
    </a>
    @endcan

    <div class="pt-4">
        <p class="px-3 text-xs font-semibold text-gray-500 uppercase tracking-wider">Reabastecimiento</p>
    </div>
    @can('reabastecimiento-ver')
    <a href="{{ route('reabastecimiento.alertas') }}" wire:navigate class="group flex items-center px-3 py-2 text-sm font-medium rounded-md {{ request()->routeIs('reabastecimiento*') ? 'bg-indigo-50 text-indigo-700' : 'text-gray-700 hover:text-gray-900 hover:bg-gray-50' }}">
        <svg class="mr-3 h-5 w-5 flex-shrink-0 {{ request()->routeIs('reabastecimiento*') ? 'text-indigo-500' : 'text-gray-400 group-hover:text-gray-500' }}" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M14.857 17.082a23.848 23.848 0 005.454-1.31A8.967 8.967 0 0118 9.75v-.7V9A6 6 0 006 9v.75a8.967 8.967 0 01-2.312 6.022c1.733.64 3.56 1.085 5.455 1.31m5.714 0a24.255 24.255 0 01-5.714 0m5.714 0a3 3 0 11-5.714 0" /></svg>
        Alertas y Compras
    </a>
    @endcan

    @canany(['sucursales-ver', 'usuarios-ver', 'roles-ver', 'catalogos-ver', 'proveedores-ver', 'compras-ver', 'ventas-ver', 'devoluciones-ver', 'inventario-ver', 'reportes-ver', 'reabastecimiento-ver'])
    @else
    <p class="px-3 text-sm text-gray-500">No tienes módulos asignados</p>
    @endcanany
</nav>
