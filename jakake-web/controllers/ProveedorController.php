<?php
namespace App\Controllers;

class ProveedorController {
    public function crear() {
        $title = 'Crear Proveedor';
        require __DIR__ . '/../views/auth/form_proveedores.php';
    }
}
