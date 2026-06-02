<?php

namespace Database\Seeders;

use App\Models\AlertaReabastecimiento;
use App\Models\Catalogo;
use App\Models\Producto;
use App\Models\Proveedor;
use App\Models\Serie;
use App\Models\Sucursal;
use App\Models\User;
use App\Models\OrdenCompra;
use Illuminate\Database\Seeder;

class DemoDataSeeder extends Seeder
{
    public function run(): void
    {
        $sucursal = Sucursal::firstOrCreate(
            ['nombre' => 'Sucursal Principal'],
            ['direccion' => 'Av. Principal 123', 'telefono' => '+56 9 1234 5678', 'activa' => true]
        );

        $sucursal2 = Sucursal::firstOrCreate(
            ['nombre' => 'Sucursal Norte'],
            ['direccion' => 'Av. Norte 456', 'telefono' => '+56 9 9876 5432', 'activa' => true]
        );

        $admin = User::where('email', 'admin@inventario.com')->first();
        if ($admin) {
            $admin->sucursales()->syncWithoutDetaching([$sucursal->id, $sucursal2->id]);
        }

        $proveedor1 = Proveedor::firstOrCreate(
            ['rut' => '76.123.456-7'],
            ['nombre' => 'Distribuidora Nacional Ltda.', 'contacto' => 'Carlos Méndez', 'telefono' => '+56 2 2345 6789', 'email' => 'contacto@distnacional.cl', 'direccion' => 'Av. del Valle 789']
        );

        $proveedor2 = Proveedor::firstOrCreate(
            ['rut' => '77.987.654-3'],
            ['nombre' => 'Importadora Global SPA', 'contacto' => 'María Torres', 'telefono' => '+56 2 8765 4321', 'email' => 'ventas@importglobal.cl', 'direccion' => 'Calle Comercio 456']
        );

        $cat1 = Catalogo::firstOrCreate(
            ['codigo' => 'CHO-NEGRA'],
            ['nombre' => 'Chocolate Negro 70%', 'descripcion' => 'Chocolate negro con 70% de cacao, presentación 100g', 'precio' => 3490, 'serie_habilitada' => true]
        );

        $cat2 = Catalogo::firstOrCreate(
            ['codigo' => 'CHO-LECHE'],
            ['nombre' => 'Chocolate con Leche', 'descripcion' => 'Chocolate con leche cremoso, presentación 100g', 'precio' => 2990, 'serie_habilitada' => true]
        );

        $cat3 = Catalogo::firstOrCreate(
            ['codigo' => 'GAL-MANTEC'],
            ['nombre' => 'Galletas de Mantequilla', 'descripcion' => 'Galletas de mantequilla artesanales, caja 200g', 'precio' => 1990, 'serie_habilitada' => false]
        );

        $cat4 = Catalogo::firstOrCreate(
            ['codigo' => 'BEB-COLA'],
            ['nombre' => 'Bebida Cola 355ml', 'descripcion' => 'Bebida gaseosa sabor cola, lata 355ml', 'precio' => 990, 'serie_habilitada' => true]
        );

        $cat5 = Catalogo::firstOrCreate(
            ['codigo' => 'ARROZ-GRANO'],
            ['nombre' => 'Arroz Grano Largo 1kg', 'descripcion' => 'Arroz de grano largo, bolsa 1kg', 'precio' => 1490, 'serie_habilitada' => false]
        );

        $cat6 = Catalogo::firstOrCreate(
            ['codigo' => 'ACEITE-OLIVA'],
            ['nombre' => 'Aceite de Oliva Extra Virgen', 'descripcion' => 'Aceite de oliva extra virgen 500ml', 'precio' => 5990, 'serie_habilitada' => true]
        );

        $cat7 = Catalogo::firstOrCreate(
            ['codigo' => 'CAFE-GRANO'],
            ['nombre' => 'Café en Grano 250g', 'descripcion' => 'Café arábico tostado en grano, bolsa 250g', 'precio' => 7990, 'serie_habilitada' => true]
        );

        $cat8 = Catalogo::firstOrCreate(
            ['codigo' => 'LECHE-ENTERA'],
            ['nombre' => 'Leche Entera 1L', 'descripcion' => 'Leche entera pasteurizada, envase 1 litro', 'precio' => 1190, 'serie_habilitada' => false]
        );

        // Orden de Compra 1
        $oc1 = OrdenCompra::firstOrCreate(
            ['numero_orden' => 'OC-2025-001'],
            [
                'proveedor_id' => $proveedor1->id,
                'sucursal_id' => $sucursal->id,
                'fecha' => now()->subDays(15),
                'observacion' => 'Primera compra del mes',
                'estado' => 'completada',
            ]
        );

        // Productos con serie (Chocolate Negro) - OC1
        $p1 = Producto::firstOrCreate(
            ['catalogo_id' => $cat1->id, 'orden_compra_id' => $oc1->id, 'sucursal_id' => $sucursal->id, 'costo_unitario' => 2500, 'cantidad' => 1],
            ['catalogo_id' => $cat1->id, 'orden_compra_id' => $oc1->id, 'sucursal_id' => $sucursal->id, 'costo_unitario' => 2500, 'cantidad' => 1]
        );

        // Create series for chocolate negro
        foreach (range(1, 10) as $i) {
            Serie::firstOrCreate(
                ['codigo_serie' => "CHO-NEGRA-$i"],
                ['producto_id' => $p1->id, 'catalogo_id' => $cat1->id, 'sucursal_id' => $sucursal->id, 'estado' => 'disponible']
            );
        }

        // Chocolate con Leche
        $p2 = Producto::firstOrCreate(
            ['catalogo_id' => $cat2->id, 'orden_compra_id' => $oc1->id, 'sucursal_id' => $sucursal->id, 'costo_unitario' => 2000, 'cantidad' => 1],
            ['catalogo_id' => $cat2->id, 'orden_compra_id' => $oc1->id, 'sucursal_id' => $sucursal->id, 'costo_unitario' => 2000, 'cantidad' => 1]
        );

        foreach (range(1, 8) as $i) {
            Serie::firstOrCreate(
                ['codigo_serie' => "CHO-LECHE-$i"],
                ['producto_id' => $p2->id, 'catalogo_id' => $cat2->id, 'sucursal_id' => $sucursal->id, 'estado' => 'disponible']
            );
        }

        // Bebida Cola
        $p3 = Producto::firstOrCreate(
            ['catalogo_id' => $cat4->id, 'orden_compra_id' => $oc1->id, 'sucursal_id' => $sucursal->id, 'costo_unitario' => 600, 'cantidad' => 1],
            ['catalogo_id' => $cat4->id, 'orden_compra_id' => $oc1->id, 'sucursal_id' => $sucursal->id, 'costo_unitario' => 600, 'cantidad' => 1]
        );

        foreach (range(1, 24) as $i) {
            Serie::firstOrCreate(
                ['codigo_serie' => "BEB-COLA-$i"],
                ['producto_id' => $p3->id, 'catalogo_id' => $cat4->id, 'sucursal_id' => $sucursal->id, 'estado' => 'disponible']
            );
        }

        // Aceite de Oliva
        $p4 = Producto::firstOrCreate(
            ['catalogo_id' => $cat6->id, 'orden_compra_id' => $oc1->id, 'sucursal_id' => $sucursal->id, 'costo_unitario' => 4000, 'cantidad' => 1],
            ['catalogo_id' => $cat6->id, 'orden_compra_id' => $oc1->id, 'sucursal_id' => $sucursal->id, 'costo_unitario' => 4000, 'cantidad' => 1]
        );

        foreach (range(1, 6) as $i) {
            Serie::firstOrCreate(
                ['codigo_serie' => "ACEITE-OLIVA-$i"],
                ['producto_id' => $p4->id, 'catalogo_id' => $cat6->id, 'sucursal_id' => $sucursal->id, 'estado' => 'disponible']
            );
        }

        // Café en Grano
        $p5 = Producto::firstOrCreate(
            ['catalogo_id' => $cat7->id, 'orden_compra_id' => $oc1->id, 'sucursal_id' => $sucursal->id, 'costo_unitario' => 5500, 'cantidad' => 1],
            ['catalogo_id' => $cat7->id, 'orden_compra_id' => $oc1->id, 'sucursal_id' => $sucursal->id, 'costo_unitario' => 5500, 'cantidad' => 1]
        );

        foreach (range(1, 5) as $i) {
            Serie::firstOrCreate(
                ['codigo_serie' => "CAFE-GRANO-$i"],
                ['producto_id' => $p5->id, 'catalogo_id' => $cat7->id, 'sucursal_id' => $sucursal->id, 'estado' => 'disponible']
            );
        }

        // Productos sin serie (Galletas Mantequilla) - OC1
        Producto::firstOrCreate(
            ['catalogo_id' => $cat3->id, 'orden_compra_id' => $oc1->id, 'sucursal_id' => $sucursal->id, 'costo_unitario' => 1200, 'cantidad' => 30]
        );

        // Arroz
        Producto::firstOrCreate(
            ['catalogo_id' => $cat5->id, 'orden_compra_id' => $oc1->id, 'sucursal_id' => $sucursal->id, 'costo_unitario' => 900, 'cantidad' => 50]
        );

        // Leche Entera
        Producto::firstOrCreate(
            ['catalogo_id' => $cat8->id, 'orden_compra_id' => $oc1->id, 'sucursal_id' => $sucursal->id, 'costo_unitario' => 800, 'cantidad' => 40]
        );

        // Orden de Compra 2 (sucursal norte)
        $oc2 = OrdenCompra::firstOrCreate(
            ['numero_orden' => 'OC-2025-002'],
            [
                'proveedor_id' => $proveedor2->id,
                'sucursal_id' => $sucursal2->id,
                'fecha' => now()->subDays(7),
                'observacion' => 'Stock sucursal norte',
                'estado' => 'completada',
            ]
        );

        $p6 = Producto::firstOrCreate(
            ['catalogo_id' => $cat1->id, 'orden_compra_id' => $oc2->id, 'sucursal_id' => $sucursal2->id, 'costo_unitario' => 2500, 'cantidad' => 1],
            ['catalogo_id' => $cat1->id, 'orden_compra_id' => $oc2->id, 'sucursal_id' => $sucursal2->id, 'costo_unitario' => 2500, 'cantidad' => 1]
        );
        foreach (range(1, 5) as $i) {
            Serie::firstOrCreate(
                ['codigo_serie' => "CHO-NEGRA-N$i"],
                ['producto_id' => $p6->id, 'catalogo_id' => $cat1->id, 'sucursal_id' => $sucursal2->id, 'estado' => 'disponible']
            );
        }

        // Alertas de reabastecimiento
        AlertaReabastecimiento::firstOrCreate(
            ['catalogo_id' => $cat1->id, 'sucursal_id' => $sucursal->id],
            ['cantidad_minima' => 3, 'tope_maximo' => 20, 'activa' => true]
        );
        AlertaReabastecimiento::firstOrCreate(
            ['catalogo_id' => $cat3->id, 'sucursal_id' => $sucursal->id],
            ['cantidad_minima' => 10, 'tope_maximo' => 60, 'activa' => true]
        );
        AlertaReabastecimiento::firstOrCreate(
            ['catalogo_id' => $cat5->id, 'sucursal_id' => $sucursal->id],
            ['cantidad_minima' => 20, 'tope_maximo' => 100, 'activa' => true]
        );
        AlertaReabastecimiento::firstOrCreate(
            ['catalogo_id' => $cat7->id, 'sucursal_id' => $sucursal->id],
            ['cantidad_minima' => 2, 'tope_maximo' => 15, 'activa' => true]
        );

        $this->command->info('Demo data seeded successfully!');
        $this->command->info("Sucursales: 2, Catalogos: 8, Proveedores: 2, Series creadas");
    }
}
