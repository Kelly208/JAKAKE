<?php
namespace App\Controllers;

class ProductoController {
    public function index() {
        // Datos simulados
        $productos = [
            ['id' => 1, 'nombre' => 'Lapicero', 'precio' => 1200, 'stock' => 50],
            ['id' => 2, 'nombre' => 'Cuaderno', 'precio' => 3500, 'stock' => 30],
        ];
        $title = 'Inventario Actual';
        require __DIR__ . '/../views/auth/inventario.php';
    }

    public function crear() {
        $title = 'Crear Producto';
        require __DIR__ . '/../views/auth/form_productos.php';
    }
}
