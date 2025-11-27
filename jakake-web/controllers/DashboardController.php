<?php
namespace App\Controllers;

use App\Config\Database;
use App\Utils\Session;
use PDO;

class DashboardController
{
    private $db;

    public function __construct()
    {
        $this->db = Database::getInstance();
    }

    public function index()
    {
        Session::start();

        // Verificar autenticación
        if (!Session::get('user_id')) {
            header('Location: /login');
            exit;
        }

        $userId = Session::get('user_id');
        $userRol = Session::get('user_rol');
        $isAdmin = $userRol === 'administrador';

        // Obtener estadísticas
        $stats = $this->getStats();
        $ventasRecientes = $this->getVentasRecientes();
        $productosStock = $this->getProductosBajoStock();
        $topProductos = $this->getTopProductos();

        // Variables para la vista
        $title = 'Dashboard - Papelería JAKAKE';

        require_once __DIR__ . '/../views/dashboard/index.php';
    }

    private function getStats()
    {
        try {
            // Total de ventas del día
            $stmt = $this->db->query("
                SELECT 
                    COUNT(*) as total_ventas,
                    COALESCE(SUM(total), 0) as total_dia
                FROM ventas 
                WHERE DATE(fecha) = CURDATE() 
                AND estado = 'completada'
            ");
            $ventasHoy = $stmt->fetch(PDO::FETCH_ASSOC);

            // Total de productos
            $stmt = $this->db->query("
                SELECT COUNT(*) as total FROM productos WHERE estado = 'activo'
            ");
            $totalProductos = $stmt->fetchColumn();

            // Total de clientes
            $stmt = $this->db->query("SELECT COUNT(*) as total FROM clientes");
            $totalClientes = $stmt->fetchColumn();

            // Productos bajo stock
            $stmt = $this->db->query("
                SELECT COUNT(*) as total 
                FROM productos 
                WHERE cantidad < cantidad_minima 
                AND estado = 'activo'
            ");
            $productosBajoStock = $stmt->fetchColumn();

            return [
                'ventas_hoy' => $ventasHoy['total_ventas'],
                'total_dia' => $ventasHoy['total_dia'],
                'total_productos' => $totalProductos,
                'total_clientes' => $totalClientes,
                'productos_bajo_stock' => $productosBajoStock
            ];

        } catch (\PDOException $e) {
            error_log("Error al obtener estadísticas: " . $e->getMessage());
            return [
                'ventas_hoy' => 0,
                'total_dia' => 0,
                'total_productos' => 0,
                'total_clientes' => 0,
                'productos_bajo_stock' => 0
            ];
        }
    }

    private function getVentasRecientes()
    {
        try {
            $stmt = $this->db->query("
                SELECT 
                    v.id,
                    v.fecha,
                    v.total,
                    v.estado,
                    c.nombre as cliente_nombre,
                    u.nombre as usuario_nombre
                FROM ventas v
                LEFT JOIN clientes c ON v.cliente_id = c.id
                LEFT JOIN usuarios u ON v.usuario_id = u.id
                ORDER BY v.fecha_registro DESC
                LIMIT 10
            ");
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (\PDOException $e) {
            error_log("Error al obtener ventas recientes: " . $e->getMessage());
            return [];
        }
    }

    private function getProductosBajoStock()
    {
        try {
            // Usar el procedimiento almacenado
            $stmt = $this->db->prepare("CALL sp_productos_bajo_stock(20)");
            $stmt->execute();
            $productos = $stmt->fetchAll(PDO::FETCH_ASSOC);
            $stmt->closeCursor();
            return $productos;
        } catch (\PDOException $e) {
            error_log("Error al obtener productos bajo stock: " . $e->getMessage());
            return [];
        }
    }

    private function getTopProductos()
    {
        try {
            // Usar el procedimiento almacenado para productos más vendidos
            $stmt = $this->db->prepare("
                CALL sp_productos_mas_vendidos(
                    DATE_SUB(CURDATE(), INTERVAL 30 DAY), 
                    CURDATE(), 
                    5
                )
            ");
            $stmt->execute();
            $productos = $stmt->fetchAll(PDO::FETCH_ASSOC);
            $stmt->closeCursor();
            return $productos;
        } catch (\PDOException $e) {
            error_log("Error al obtener top productos: " . $e->getMessage());
            return [];
        }
    }
}
//juan ya casi es 30 