<?php
namespace App\Controllers;

use App\Models\Producto;
use App\Utils\Security;
use App\Utils\Session;

class ProductoController {
    // Listar productos
    public function index() {
        Session::start();
        Session::requireLogin();
        
        $productoModel = new Producto();
        $productos = $productoModel->all(100, 0);
        
        require __DIR__ . '/../views/productos/index.php';
    }

    // Crear producto
    public function crear() {
        Session::start();
        Session::requireLogin();
        Session::requireRole(['Administrador']);
        
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $token = $_POST['csrf_token'] ?? '';
            if (!Security::validateToken($token)) {
                die("Token CSRF inválido");
            }
            
            $data = [
                'codigo_sku' => Security::sanitize($_POST['sku']),
                'nombre' => Security::sanitize($_POST['nombre']),
                'descripcion' => Security::sanitize($_POST['descripcion']),
                'categoria_id' => (int)$_POST['categoria_id'],
                'unidad_medida' => Security::sanitize($_POST['unidad']),
                'precio_costo' => (float)$_POST['precio_costo'],
                'precio_venta' => (float)$_POST['precio_venta'],
                'stock_minimo' => (int)$_POST['stock_minimo'],
                'fecha_ingreso' => date('Y-m-d')
            ];
            
            $productoModel = new Producto();
            $productoModel->create($data);
            
            header('Location: /productos?success=1');
            exit;
        }
        
        // Obtener categorías
        $pdo = \App\Config\Database::getInstance();
        $categorias = $pdo->query("SELECT * FROM categorias_productos WHERE estado = 'activo'")->fetchAll();
        
        require __DIR__ . '/../views/productos/crear.php';
    }

    // Buscar productos (para ventas)
    public function buscar() {
        Session::start();
        Session::requireLogin();
        
        header('Content-Type: application/json');
        
        $term = Security::sanitize($_GET['q'] ?? '');
        if (strlen($term) < 2) {
            echo json_encode([]);
            exit;
        }
        
        $productoModel = new Producto();
        $resultados = $productoModel->search($term);
        
        echo json_encode($resultados);
    }
}