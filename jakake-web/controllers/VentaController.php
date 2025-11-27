<?php
namespace App\Controllers;

use App\Models\VentaModel;
use App\Utils\Security;

class VentaController {
    public function registrar() {
        $title = 'Registrar Venta';
        require __DIR__ . '/../views/auth/form_ventas.php';
    }

    public function procesar() {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') die('Método inválido');
        if (!Security::validateToken($_POST['csrf_token'] ?? '')) die('Token inválido');
        $data = Security::sanitize($_POST);
        $model = new VentaModel();
        $model->registrarVenta($data);
        header('Location: /ventas/registrar'); exit;
    }

    public function periodo() {
        $title = 'Ventas por Período';
        $ventas = [];
        if (isset($_GET['desde'], $_GET['hasta'])) {
            $model = new VentaModel();
            $ventas = $model->ventasPorPeriodo($_GET['desde'], $_GET['hasta']);
        }
        require __DIR__ . '/../views/auth/ventas_periodo.php';
    }
}
