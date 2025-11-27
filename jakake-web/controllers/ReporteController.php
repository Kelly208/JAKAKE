<?php
namespace App\Controllers;

use App\Config\Database;
use App\Utils\Session;
use PDO;

class ReporteController
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

        $title = 'Reportes - Papelería JAKAKE';

        require_once __DIR__ . '/../views/reportes/index.php';
    }

    public function ventas()
    {
        Session::start();
        if (!Session::get('user_id')) {
            header('Location: /login');
            exit;
        }

        $title = 'Reporte de Ventas - Papelería JAKAKE';

        // Obtener fechas del formulario o usar valores por defecto
        $fecha_inicio = $_GET['fecha_inicio'] ?? date('Y-m-01'); // Primer día del mes
        $fecha_fin = $_GET['fecha_fin'] ?? date('Y-m-d'); // Hoy

        try {
            // Resumen general del período
            $stmt = $this->db->prepare("
                SELECT 
                    COUNT(*) as total_ventas,
                    COALESCE(SUM(total), 0) as monto_total,
                    COALESCE(AVG(total), 0) as promedio_venta,
                    COUNT(DISTINCT cliente_id) as clientes_unicos
                FROM ventas
                WHERE DATE(fecha) BETWEEN ? AND ?
                AND estado = 'completada'
            ");
            $stmt->execute([$fecha_inicio, $fecha_fin]);
            $resumen = $stmt->fetch(PDO::FETCH_ASSOC);

            // Ventas por día
            $stmt = $this->db->prepare("
                SELECT 
                    DATE(fecha) as fecha,
                    COUNT(*) as cantidad_ventas,
                    SUM(total) as monto_total
                FROM ventas
                WHERE DATE(fecha) BETWEEN ? AND ?
                AND estado = 'completada'
                GROUP BY DATE(fecha)
                ORDER BY fecha DESC
            ");
            $stmt->execute([$fecha_inicio, $fecha_fin]);
            $ventas_diarias = $stmt->fetchAll(PDO::FETCH_ASSOC);

            // Ventas por medio de pago
            $stmt = $this->db->prepare("
                SELECT 
                    medio_pago,
                    COUNT(*) as cantidad,
                    SUM(total) as monto_total,
                    ROUND((COUNT(*) * 100.0 / (SELECT COUNT(*) FROM ventas WHERE DATE(fecha) BETWEEN ? AND ? AND estado = 'completada')), 2) as porcentaje
                FROM ventas
                WHERE DATE(fecha) BETWEEN ? AND ?
                AND estado = 'completada'
                GROUP BY medio_pago
                ORDER BY cantidad DESC
            ");
            $stmt->execute([$fecha_inicio, $fecha_fin, $fecha_inicio, $fecha_fin]);
            $ventas_por_medio = $stmt->fetchAll(PDO::FETCH_ASSOC);

            // Top 10 clientes
            $stmt = $this->db->prepare("
                SELECT 
                    c.nombre,
                    c.apellido,
                    c.cedula,
                    COUNT(v.id) as total_compras,
                    SUM(v.total) as monto_total
                FROM ventas v
                JOIN clientes c ON v.cliente_id = c.id
                WHERE DATE(v.fecha) BETWEEN ? AND ?
                AND v.estado = 'completada'
                GROUP BY v.cliente_id, c.nombre, c.apellido, c.cedula
                ORDER BY monto_total DESC
                LIMIT 10
            ");
            $stmt->execute([$fecha_inicio, $fecha_fin]);
            $top_clientes = $stmt->fetchAll(PDO::FETCH_ASSOC);

        } catch (\PDOException $e) {
            error_log("Error al generar reporte de ventas: " . $e->getMessage());
            $resumen = [];
            $ventas_diarias = [];
            $ventas_por_medio = [];
            $top_clientes = [];
        }

        require_once __DIR__ . '/../views/reportes/ventas.php';
    }

    public function productos()
    {
        Session::start();
        if (!Session::get('user_id')) {
            header('Location: /login');
            exit;
        }

        $title = 'Reporte de Productos - Papelería JAKAKE';

        // Obtener fechas del formulario o usar valores por defecto
        $fecha_inicio = $_GET['fecha_inicio'] ?? date('Y-m-01');
        $fecha_fin = $_GET['fecha_fin'] ?? date('Y-m-d');

        try {
            // Usar procedimiento almacenado para productos más vendidos
            $stmt = $this->db->prepare("CALL sp_productos_mas_vendidos(?, ?, 20)");
            $stmt->execute([$fecha_inicio, $fecha_fin]);
            $productos_mas_vendidos = $stmt->fetchAll(PDO::FETCH_ASSOC);
            $stmt->closeCursor();

            // Productos con bajo stock
            $stmt = $this->db->prepare("CALL sp_productos_bajo_stock(20)");
            $stmt->execute();
            $productos_bajo_stock = $stmt->fetchAll(PDO::FETCH_ASSOC);
            $stmt->closeCursor();

            // Productos por tipo
            $stmt = $this->db->query("
                SELECT 
                    tipo,
                    COUNT(*) as cantidad,
                    SUM(cantidad) as stock_total,
                    ROUND(AVG(precio), 0) as precio_promedio
                FROM productos
                WHERE estado = 'activo'
                GROUP BY tipo
                ORDER BY cantidad DESC
            ");
            $productos_por_tipo = $stmt->fetchAll(PDO::FETCH_ASSOC);

            // Resumen de inventario
            $stmt = $this->db->query("
                SELECT 
                    COUNT(*) as total_productos,
                    SUM(cantidad) as unidades_totales,
                    SUM(cantidad * precio) as valor_inventario,
                    COUNT(CASE WHEN cantidad < cantidad_minima THEN 1 END) as productos_bajo_stock
                FROM productos
                WHERE estado = 'activo'
            ");
            $resumen_inventario = $stmt->fetch(PDO::FETCH_ASSOC);

        } catch (\PDOException $e) {
            error_log("Error al generar reporte de productos: " . $e->getMessage());
            $productos_mas_vendidos = [];
            $productos_bajo_stock = [];
            $productos_por_tipo = [];
            $resumen_inventario = [];
        }

        require_once __DIR__ . '/../views/reportes/productos.php';
    }

    public function clientes()
    {
        Session::start();
        if (!Session::get('user_id')) {
            header('Location: /login');
            exit;
        }

        $title = 'Reporte de Clientes - Papelería JAKAKE';

        try {
            // Resumen general de clientes
            $stmt = $this->db->query("
                SELECT 
                    COUNT(*) as total_clientes,
                    COUNT(CASE WHEN estado = 'activo' THEN 1 END) as clientes_activos,
                    COUNT(CASE WHEN estado = 'inactivo' THEN 1 END) as clientes_inactivos
                FROM clientes
            ");
            $resumen_clientes = $stmt->fetch(PDO::FETCH_ASSOC);

            // Clientes con más compras
            $stmt = $this->db->query("
                SELECT 
                    c.cedula,
                    c.nombre,
                    c.apellido,
                    c.email,
                    c.telefono,
                    COUNT(v.id) as total_compras,
                    COALESCE(SUM(v.total), 0) as monto_total,
                    MAX(v.fecha) as ultima_compra
                FROM clientes c
                LEFT JOIN ventas v ON c.id = v.cliente_id AND v.estado = 'completada'
                GROUP BY c.id, c.cedula, c.nombre, c.apellido, c.email, c.telefono
                HAVING total_compras > 0
                ORDER BY monto_total DESC
                LIMIT 20
            ");
            $top_clientes = $stmt->fetchAll(PDO::FETCH_ASSOC);

            // Clientes nuevos (último mes)
            $stmt = $this->db->query("
                SELECT 
                    c.cedula,
                    c.nombre,
                    c.apellido,
                    c.fecha_registro,
                    COUNT(v.id) as total_compras
                FROM clientes c
                LEFT JOIN ventas v ON c.id = v.cliente_id
                WHERE c.fecha_registro >= DATE_SUB(CURDATE(), INTERVAL 30 DAY)
                GROUP BY c.id, c.cedula, c.nombre, c.apellido, c.fecha_registro
                ORDER BY c.fecha_registro DESC
                LIMIT 10
            ");
            $clientes_nuevos = $stmt->fetchAll(PDO::FETCH_ASSOC);

            // Clientes inactivos (sin compras en 90 días)
            $stmt = $this->db->query("
                SELECT 
                    c.cedula,
                    c.nombre,
                    c.apellido,
                    c.email,
                    c.ultima_compra,
                    DATEDIFF(CURDATE(), c.ultima_compra) as dias_inactivo
                FROM clientes c
                WHERE c.ultima_compra IS NOT NULL
                AND c.ultima_compra < DATE_SUB(CURDATE(), INTERVAL 90 DAY)
                ORDER BY c.ultima_compra ASC
                LIMIT 10
            ");
            $clientes_inactivos = $stmt->fetchAll(PDO::FETCH_ASSOC);

        } catch (\PDOException $e) {
            error_log("Error al generar reporte de clientes: " . $e->getMessage());
            $resumen_clientes = [];
            $top_clientes = [];
            $clientes_nuevos = [];
            $clientes_inactivos = [];
        }

        require_once __DIR__ . '/../views/reportes/clientes.php';
    }
}
