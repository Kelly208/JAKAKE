<?php
namespace App\Controllers;

class ReporteController {
    public function topProductos() {
        $topProductos = [
            ['nombre' => 'Cuaderno', 'ventas' => 120],
            ['nombre' => 'Lapicero', 'ventas' => 80],
        ];
        $title = 'Productos Más Vendidos';
        require __DIR__ . '/../views/auth/productos_top.php';
    }

    public function bonosActivos() {
        $bonos = [
            ['codigo' => 'BONO10', 'descripcion' => '10% descuento en útiles'],
            ['codigo' => 'BONO20', 'descripcion' => '20% descuento en papelería'],
        ];
        $title = 'Bonos Activos';
        require __DIR__ . '/../views/auth/bonos.php';
    }

    public function auditoriaReciente() {
        $auditoria = [
            ['accion' => 'Login', 'usuario' => 'admin', 'fecha' => '2025-11-27'],
            ['accion' => 'Registro de venta', 'usuario' => 'juan', 'fecha' => '2025-11-27'],
        ];
        $title = 'Auditoría Reciente';
        require __DIR__ . '/../views/auth/auditoria.php';
    }
}
