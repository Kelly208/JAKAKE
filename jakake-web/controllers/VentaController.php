<?php
namespace App\Controllers;

use App\Config\Database;
use App\Utils\Session;
use App\Utils\Security;
use PDO;

class VentaController
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

        $title = 'Ventas - Papelería JAKAKE';
        
        // Obtener todas las ventas con información relacionada
        try {
            $stmt = $this->db->query("
                SELECT 
                    v.*,
                    c.nombre as cliente_nombre,
                    c.apellido as cliente_apellido,
                    u.nombre as cajero_nombre,
                    (SELECT COUNT(*) FROM detalle_ventas WHERE venta_id = v.id) as total_productos
                FROM ventas v
                LEFT JOIN clientes c ON v.cliente_id = c.id
                LEFT JOIN usuarios u ON v.usuario_id = u.id
                ORDER BY v.fecha DESC
                LIMIT 100
            ");
            $ventas = $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (\PDOException $e) {
            error_log("Error al obtener ventas: " . $e->getMessage());
            $ventas = [];
        }

        require_once __DIR__ . '/../views/ventas/index.php';
    }

    public function nueva()
    {
        Session::start();
        if (!Session::get('user_id')) {
            header('Location: /login');
            exit;
        }

        $title = 'Nueva Venta - Papelería JAKAKE';

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $this->procesarVenta();
            return;
        }

        // Obtener clientes activos
        $clientes = $this->getClientesActivos();
        
        // Obtener productos activos con stock
        $productos = $this->getProductosDisponibles();

        require_once __DIR__ . '/../views/ventas/nueva.php';
    }

    private function procesarVenta()
    {
        try {
            $cliente_id = intval($_POST['cliente_id']);
            $medio_pago = Security::sanitize($_POST['medio_pago']);
            $productos_json = $_POST['productos']; // JSON string
            
            // Validar que haya productos
            $productos = json_decode($productos_json, true);
            if (empty($productos)) {
                throw new \Exception("Debe agregar al menos un producto");
            }

            // Preparar el JSON en el formato que espera el procedimiento
            $productos_array = [];
            foreach ($productos as $prod) {
                $productos_array[] = [
                    'producto_id' => intval($prod['producto_id']),
                    'cantidad' => intval($prod['cantidad']),
                    'precio' => floatval($prod['precio'])
                ];
            }

            $productos_json_final = json_encode($productos_array);
            $usuario_id = Session::get('user_id');

            // Llamar al procedimiento almacenado sp_registrar_venta_completa
            $stmt = $this->db->prepare("
                CALL sp_registrar_venta_completa(?, ?, ?, ?)
            ");
            
            $stmt->execute([
                $cliente_id,
                $usuario_id,
                $medio_pago,
                $productos_json_final
            ]);

            // Obtener el ID de la venta creada (el procedimiento lo devuelve)
            $result = $stmt->fetch(PDO::FETCH_ASSOC);
            $venta_id = $result['venta_id'] ?? null;

            Session::set('success', 'Venta registrada exitosamente. ID: #' . $venta_id);
            header('Location: /ventas');
            exit;

        } catch (\Exception $e) {
            error_log("Error al procesar venta: " . $e->getMessage());
            Session::set('error', 'Error al procesar venta: ' . $e->getMessage());
            header('Location: /ventas/nueva');
            exit;
        }
    }

    public function detalle($id)
    {
        Session::start();
        if (!Session::get('user_id')) {
            header('Location: /login');
            exit;
        }

        $title = 'Detalle de Venta - Papelería JAKAKE';

        try {
            // Obtener información de la venta
            $stmt = $this->db->prepare("
                SELECT 
                    v.*,
                    c.cedula as cliente_cedula,
                    c.nombre as cliente_nombre,
                    c.apellido as cliente_apellido,
                    c.telefono as cliente_telefono,
                    c.email as cliente_email,
                    u.nombre as cajero_nombre
                FROM ventas v
                LEFT JOIN clientes c ON v.cliente_id = c.id
                LEFT JOIN usuarios u ON v.usuario_id = u.id
                WHERE v.id = ?
            ");
            $stmt->execute([$id]);
            $venta = $stmt->fetch(PDO::FETCH_ASSOC);

            if (!$venta) {
                Session::set('error', 'Venta no encontrada');
                header('Location: /ventas');
                exit;
            }

            // Obtener detalle de productos
            $stmt = $this->db->prepare("
                SELECT 
                    dv.*,
                    p.nombre as producto_nombre,
                    p.codigo as producto_codigo
                FROM detalle_ventas dv
                JOIN productos p ON dv.producto_id = p.id
                WHERE dv.venta_id = ?
            ");
            $stmt->execute([$id]);
            $detalles = $stmt->fetchAll(PDO::FETCH_ASSOC);

        } catch (\PDOException $e) {
            error_log("Error al obtener detalle de venta: " . $e->getMessage());
            Session::set('error', 'Error al obtener detalle de venta');
            header('Location: /ventas');
            exit;
        }

        require_once __DIR__ . '/../views/ventas/detalle.php';
    }

    private function getClientesActivos()
    {
        try {
            $stmt = $this->db->query("
                SELECT id, cedula, nombre, apellido 
                FROM clientes 
                WHERE estado = 'activo' 
                ORDER BY nombre ASC
            ");
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (\PDOException $e) {
            error_log("Error al obtener clientes: " . $e->getMessage());
            return [];
        }
    }

    private function getProductosDisponibles()
    {
        try {
            $stmt = $this->db->query("
                SELECT 
                    p.id,
                    p.codigo,
                    p.nombre,
                    p.precio,
                    p.cantidad,
                    p.cantidad_minima,
                    p.tipo
                FROM productos p
                WHERE p.estado = 'activo' AND p.cantidad > 0
                ORDER BY p.nombre ASC
            ");
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (\PDOException $e) {
            error_log("Error al obtener productos: " . $e->getMessage());
            return [];
        }
    }
}
