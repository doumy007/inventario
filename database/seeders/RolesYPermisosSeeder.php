<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;
use App\Models\User;

class RolesYPermisosSeeder extends Seeder
{
    public function run(): void
    {
        $permisos = [
            'sucursales-ver', 'sucursales-crear', 'sucursales-editar', 'sucursales-eliminar',
            'usuarios-ver', 'usuarios-crear', 'usuarios-editar', 'usuarios-eliminar',
            'roles-ver', 'roles-crear', 'roles-editar', 'roles-eliminar',
            'catalogos-ver', 'catalogos-crear', 'catalogos-editar', 'catalogos-eliminar',
            'proveedores-ver', 'proveedores-crear', 'proveedores-editar', 'proveedores-eliminar',
            'compras-ver', 'compras-crear', 'compras-editar', 'compras-eliminar',
            'ventas-ver', 'ventas-crear', 'ventas-editar', 'ventas-eliminar',
            'devoluciones-ver', 'devoluciones-crear', 'devoluciones-editar', 'devoluciones-eliminar',
            'inventario-ver',
            'reportes-ver',
            'reabastecimiento-ver', 'reabastecimiento-crear', 'reabastecimiento-editar', 'reabastecimiento-eliminar',
        ];

        foreach ($permisos as $permiso) {
            Permission::firstOrCreate(['name' => $permiso, 'guard_name' => 'web']);
        }

        // Super Admin: todos los permisos
        $superAdmin = Role::firstOrCreate(['name' => 'Super Admin', 'guard_name' => 'web']);
        $superAdmin->syncPermissions($permisos);

        // Admin: todos excepto eliminar
        $admin = Role::firstOrCreate(['name' => 'Admin', 'guard_name' => 'web']);
        $admin->syncPermissions(array_filter($permisos, fn ($p) => !str_ends_with($p, '-eliminar')));

        // Vendedor
        $vendedor = Role::firstOrCreate(['name' => 'Vendedor', 'guard_name' => 'web']);
        $vendedor->syncPermissions([
            'ventas-ver', 'ventas-crear',
            'devoluciones-ver', 'devoluciones-crear',
            'catalogos-ver',
            'inventario-ver',
        ]);

        // Bodeguero
        $bodeguero = Role::firstOrCreate(['name' => 'Bodeguero', 'guard_name' => 'web']);
        $bodeguero->syncPermissions([
            'catalogos-ver', 'catalogos-crear',
            'proveedores-ver',
            'compras-ver', 'compras-crear',
            'inventario-ver',
        ]);

        // Gerente: todos los permisos -ver, reportes-ver, reabastecimiento-ver
        $gerente = Role::firstOrCreate(['name' => 'Gerente', 'guard_name' => 'web']);
        $gerente->syncPermissions([
            'sucursales-ver', 'usuarios-ver', 'roles-ver',
            'catalogos-ver', 'proveedores-ver', 'compras-ver',
            'ventas-ver', 'devoluciones-ver',
            'inventario-ver', 'reportes-ver', 'reabastecimiento-ver',
        ]);

        $user = User::firstOrCreate(
            ['email' => 'admin@inventario.com'],
            [
                'name' => 'Super Admin',
                'password' => bcrypt('password'),
            ]
        );
        $user->assignRole('Super Admin');
    }
}
