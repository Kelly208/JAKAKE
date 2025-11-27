<?php
namespace App\Controllers;

use App\Models\ProveedorModel;
use App\Utils\Security;

class ProveedorController {
    public function crear() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            if (!Security::validateToken($_POST['csrf_token'] ?? '')) die('Token inválido');
            $data = Security::sanitize($_POST);
            $model = new ProveedorModel();
            $model->crear($data);
            header('Location: /proveedores/crear'); exit;
        }
        $title = 'Crear Proveedor';
        require __DIR__ . '/../views/auth/form_proveedores.php';
    }
}
