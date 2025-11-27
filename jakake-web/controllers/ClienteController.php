<?php
namespace App\Controllers;

class ClienteController {
    public function crear() {
        $title = 'Crear Cliente';
        require __DIR__ . '/../views/auth/form_clientes.php';
    }
}
