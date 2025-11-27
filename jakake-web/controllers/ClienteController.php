<?php
namespace App\Controllers;

use App\Models\ClienteModel;
use App\Utils\Security;

class ClienteController {
    public function crear() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            if (!Security::validateToken($_POST['csrf_token'] ?? '')) die('Token inválido');
            $data = Security::sanitize($_POST);
            $model = new ClienteModel();
            $model->crear($data);
            header('Location: /clientes/crear'); exit;
        }
        $title = 'Crear Cliente';
        require __DIR__ . '/../views/auth/form_clientes.php';
    }
}
