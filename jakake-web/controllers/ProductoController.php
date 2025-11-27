<?php
namespace App\Controllers;

use App\Config\Database;
use App\Utils\Session;
use App\Utils\Security;
use PDO;

class ProductoController
{
    private $db;

    public function __construct()
    {
        $this->db = Database::getInstance();
    }

    public function index()
    {
        Session::start();
        if (!Session::get('user_id')) {
            header('Location: /login');
            exit;
        }

        $title = 'Productos - Papelería JAKAKE';
        
        // Obtener todos los productos con sus proveedores
        try {
            $stmt = $this->db->query("
                SELECT 
                    p.*,
                    prov.nombre as proveedor_nombre
                FROM productos p
                LEFT JOIN proveedores prov ON p.proveedor_id = prov.id
                ORDER BY p.nombre ASC
            ");
            $productos = $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (\PDOException $e) {
            error_log("Error al obtener productos: " . $e->getMessage());
            $productos = [];
        }

        require_once __DIR__ . '/../views/productos/index.php';
    }

    public function crear()
    {
        Session::start();
        if (!Session::get('user_id')) {
            header('Location: /login');
            exit;
        }

        $title = 'Nuevo Producto - Papelería JAKAKE';

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $this->procesarCrear();
            return;
        }

        // Obtener proveedores activos
        $proveedores = $this->getProveedoresActivos();

        require_once __DIR__ . '/../views/productos/crear.php';
    }

    private function procesarCrear()
    {
        try {
            $codigo = Security::sanitize($_POST['codigo']);
            $nombre = Security::sanitize($_POST['nombre']);
            $descripcion = Security::sanitize($_POST['descripcion'] ?? '');
            $precio = floatval($_POST['precio']);
            $cantidad = intval($_POST['cantidad']);
            $cantidad_minima = intval($_POST['cantidad_minima'] ?? 10);
            $proveedor_id = intval($_POST['proveedor_id']);
            $tipo = Security::sanitize($_POST['tipo']);

            $stmt = $this->db->prepare("
                INSERT INTO productos 
                (codigo, nombre, descripcion, precio, cantidad, cantidad_minima, proveedor_id, tipo, estado)
                VALUES (?, ?, ?, ?, ?, ?, ?, ?, 'activo')
            ");
            
            $stmt->execute([
                $codigo, $nombre, $descripcion, $precio, 
                $cantidad, $cantidad_minima, $proveedor_id, $tipo
            ]);

            Session::set('success', 'Producto creado exitosamente');
            header('Location: /productos');
            exit;

        } catch (\PDOException $e) {
            error_log("Error al crear producto: " . $e->getMessage());
            Session::set('error', 'Error al crear producto: ' . $e->getMessage());
            header('Location: /productos/crear');
            exit;
        }
    }

    public function editar($id)
    {
        Session::start();
        if (!Session::get('user_id')) {
            header('Location: /login');
            exit;
        }

        $title = 'Editar Producto - Papelería JAKAKE';

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $this->procesarEditar($id);
            return;
        }

        // Obtener producto
        try {
            $stmt = $this->db->prepare("SELECT * FROM productos WHERE id = ?");
            $stmt->execute([$id]);
            $producto = $stmt->fetch(PDO::FETCH_ASSOC);

            if (!$producto) {
                Session::set('error', 'Producto no encontrado');
                header('Location: /productos');
                exit;
            }
        } catch (\PDOException $e) {
            error_log("Error al obtener producto: " . $e->getMessage());
            Session::set('error', 'Error al obtener producto');
            header('Location: /productos');
            exit;
        }

        // Obtener proveedores
        $proveedores = $this->getProveedoresActivos();

        require_once __DIR__ . '/../views/productos/editar.php';
    }

    private function procesarEditar($id)
    {
        try {
            $nombre = Security::sanitize($_POST['nombre']);
            $descripcion = Security::sanitize($_POST['descripcion'] ?? '');
            $precio = floatval($_POST['precio']);
            $cantidad = intval($_POST['cantidad']);
            $cantidad_minima = intval($_POST['cantidad_minima'] ?? 10);
            $proveedor_id = intval($_POST['proveedor_id']);
            $tipo = Security::sanitize($_POST['tipo']);
            $estado = Security::sanitize($_POST['estado']);

            $stmt = $this->db->prepare("
                UPDATE productos 
                SET nombre = ?, descripcion = ?, precio = ?, cantidad = ?,
                    cantidad_minima = ?, proveedor_id = ?, tipo = ?, estado = ?
                WHERE id = ?
            ");
            
            $stmt->execute([
                $nombre, $descripcion, $precio, $cantidad,
                $cantidad_minima, $proveedor_id, $tipo, $estado, $id
            ]);

            Session::set('success', 'Producto actualizado exitosamente');
            header('Location: /productos');
            exit;

        } catch (\PDOException $e) {
            error_log("Error al actualizar producto: " . $e->getMessage());
            Session::set('error', 'Error al actualizar producto');
            header('Location: /productos/editar/' . $id);
            exit;
        }
    }

    private function getProveedoresActivos()
    {
        try {
            $stmt = $this->db->query("
                SELECT id, nombre, nit 
                FROM proveedores 
                WHERE estado = 'activo' 
                ORDER BY nombre ASC
            ");
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (\PDOException $e) {
            error_log("Error al obtener proveedores: " . $e->getMessage());
            return [];
        }
    }
}
