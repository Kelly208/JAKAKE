<?php
namespace App\Controllers;

class VentaController {
    public function registrar() {
        $title = 'Registrar Venta';
        require __DIR__ . '/../views/auth/form_ventas.php';
    }

    public function procesar() {
        // Simulación de registro exitoso
        echo "Venta registrada (simulada).";
    }

    public function periodo() {
        $ventas = [
            ['id' => 1, 'cliente' => 'Juan', 'producto' => 'Cuaderno', 'cantidad' => 2, 'total' => 7000],
            ['id' => 2, 'cliente' => 'Ana', 'producto' => 'Lapicero', 'cantidad' => 5, 'total' => 6000],
        ];
        $title = 'Ventas por Período';
        require __DIR__ . '/../views/auth/ventas_periodo.php';
    }
}
