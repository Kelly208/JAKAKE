<?php
namespace App\Controllers;

use App\Models\ProductoModel;
use App\Models\ReporteModel;

class ReporteController {
    public function topProductos() {
        $model = new ProductoModel();
        $topProductos = $model->productosMasVendidos();
        $title = 'Productos Más Vendidos';
        require __DIR__ . '/../views/auth/productos_top.php';
    }

    public function bonosActivos() {
        $model = new ReporteModel();
        $bonos = $model->bonosActivos();
        $title = 'Bonos Activos';
        require __DIR__ . '/../views/auth/bonos.php';
    }

    public function auditoriaReciente() {
        $model = new ReporteModel();
        $auditoria = $model->auditoriaReciente();
        $title = 'Auditoría Reciente';
        require __DIR__ . '/../views/auth/auditoria.php';
    }
}
