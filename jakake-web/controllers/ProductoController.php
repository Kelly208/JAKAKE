<?php
namespace App\Controllers;

use App\Models\ProductoModel;
use App\Utils\Security;

class ProductoController {
    public function index() {
        $model = new ProductoModel();
        $productos = $model->inventarioActual();
        $title = 'Inventario Actual';
        require __DIR__ . '/../views/auth/inventario.php';
    }

    public function crear() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            if (!Security::validateToken($_POST['csrf_token'] ?? '')) die('Token inválido');
            $data = Security::sanitize($_POST);
            $model = new ProductoModel();
            $model->crear($data);
            header('Location: /productos'); exit;
        }
        $title = 'Crear Producto';
        require __DIR__ . '/../views/auth/form_productos.php';
    }
}
