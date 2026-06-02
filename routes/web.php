<?php

use Illuminate\Support\Facades\Route;
use Livewire\Volt\Volt;

Route::view('/', 'welcome');

Route::middleware(['auth', 'verified'])->group(function () {
    Volt::route('dashboard', 'dashboard')->name('dashboard');

    // Sucursales
    Volt::route('sucursales', 'sucursales.index')->name('sucursales.index');
    Volt::route('sucursales/create', 'sucursales.create')->name('sucursales.create');
    Volt::route('sucursales/{id}/edit', 'sucursales.edit')->name('sucursales.edit');

    // Usuarios
    Volt::route('usuarios', 'usuarios.index')->name('usuarios.index');
    Volt::route('usuarios/create', 'usuarios.create')->name('usuarios.create');
    Volt::route('usuarios/{id}/edit', 'usuarios.edit')->name('usuarios.edit');

    // Roles
    Volt::route('roles', 'roles.index')->name('roles.index');
    Volt::route('roles/create', 'roles.create')->name('roles.create');
    Volt::route('roles/{id}/edit', 'roles.edit')->name('roles.edit');

    // Catálogo
    Volt::route('catalogos', 'catalogos.index')->name('catalogos.index');
    Volt::route('catalogos/create', 'catalogos.create')->name('catalogos.create');
    Volt::route('catalogos/{id}/edit', 'catalogos.edit')->name('catalogos.edit');

    // Proveedores
    Volt::route('proveedores', 'proveedores.index')->name('proveedores.index');
    Volt::route('proveedores/create', 'proveedores.create')->name('proveedores.create');
    Volt::route('proveedores/{id}/edit', 'proveedores.edit')->name('proveedores.edit');

    // Órdenes de Compra
    Volt::route('ordenes-compra', 'ordenes-compra.index')->name('ordenes-compra.index');
    Volt::route('ordenes-compra/create', 'ordenes-compra.create')->name('ordenes-compra.create');
    Volt::route('ordenes-compra/{id}', 'ordenes-compra.show')->name('ordenes-compra.show');

    // Ventas
    Volt::route('ventas', 'ventas.index')->name('ventas.index');
    Volt::route('ventas/create', 'ventas.create')->name('ventas.create');
    Volt::route('ventas/{id}', 'ventas.show')->name('ventas.show');

    // Devoluciones
    Volt::route('devoluciones/create', 'devoluciones.create')->name('devoluciones.create');
    Volt::route('devoluciones', 'devoluciones.index')->name('devoluciones.index');
    Volt::route('devoluciones/{id}/show', 'devoluciones.show')->name('devoluciones.show');

    // Inventario / Stock
    Volt::route('inventario/stock', 'inventario.stock')->name('inventario.stock');

    // Reportes
    Volt::route('reportes/ventas', 'reportes.ventas')->name('reportes.ventas');

    // Reabastecimiento
    Volt::route('reabastecimiento/alertas', 'reabastecimiento.alertas')->name('reabastecimiento.alertas');
    Volt::route('reabastecimiento/informe', 'reabastecimiento.informe')->name('reabastecimiento.informe');

    // Profile
    Route::view('profile', 'profile')->name('profile');
});

require __DIR__.'/auth.php';
