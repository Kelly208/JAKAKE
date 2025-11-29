<?php
namespace App\Controllers;

use App\Config\Database;
use App\Utils\Session;
use App\Utils\Security;
use PDO;

class DevolucionController
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

        $title = 'Devoluciones - Papelería JAKAKE';
        
        // Obtener todas las devoluciones (agrupadas por id para evitar duplicados)
        try {
            $stmt = $this->db->query("
                SELECT 
                    d.id,
                    d.venta_id,
                    d.motivo,
                    d.usuario_id,
                    d.fecha_devolucion,
                    d.fecha_registro,
                    v.fecha as fecha_venta,
                    c.nombre as cliente_nombre,
                    u.nombre as usuario_nombre,
                    COUNT(d.producto_id) as total_productos,
                    SUM(d.valor_devolucion) as valor_total
                FROM devoluciones d
                JOIN ventas v ON d.venta_id = v.id
                LEFT JOIN clientes c ON v.cliente_id = c.id
                JOIN usuarios u ON d.usuario_id = u.id
                GROUP BY d.id, d.venta_id, d.motivo, d.usuario_id, d.fecha_devolucion, 
                         d.fecha_registro, v.fecha, c.nombre, u.nombre
                ORDER BY d.fecha_devolucion DESC
                LIMIT 100
            ");
            $devoluciones = $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (\PDOException $e) {
            error_log("Error al obtener devoluciones: " . $e->getMessage());
            $devoluciones = [];
        }

        require_once __DIR__ . '/../views/devoluciones/index.php';
    }

    public function nueva()
    {
        Session::start();
        if (!Session::get('user_id')) {
            header('Location: /login');
            exit;
        }

        $title = 'Nueva Devolución - Papelería JAKAKE';

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $this->procesarDevolucion();
            return;
        }

        // Si se proporciona un ID de venta, cargar sus detalles
        $venta = null;
        $detalles = [];
        
        if (isset($_GET['venta_id'])) {
            $venta_id = intval($_GET['venta_id']);
            $venta = $this->getVentaDetalle($venta_id);
            if ($venta) {
                $detalles = $this->getVentaProductos($venta_id);
            }
        }

        require_once __DIR__ . '/../views/devoluciones/nueva.php';
    }

    private function procesarDevolucion()
    {
        try {
            $venta_id = intval($_POST['venta_id']);
            $motivo = Security::sanitize($_POST['motivo']);
            $productos_json = $_POST['productos']; // JSON string
            
            // Validar que haya productos
            $productos = json_decode($productos_json, true);
            if (empty($productos)) {
                throw new \Exception("Debe seleccionar al menos un producto para devolver");
            }

            // Preparar el JSON en el formato que espera el procedimiento
            $productos_array = [];
            foreach ($productos as $prod) {
                $productos_array[] = [
                    'producto_id' => intval($prod['producto_id']),
                    'cantidad' => intval($prod['cantidad'])
                ];
            }

            $productos_json_final = json_encode($productos_array);
            $usuario_id = Session::get('user_id');

            // Llamar al procedimiento almacenado sp_procesar_devolucion
            $stmt = $this->db->prepare("
                CALL sp_procesar_devolucion(?, ?, ?, ?)
            ");
            
            $stmt->execute([
                $venta_id,
                $usuario_id,
                $motivo,
                $productos_json_final
            ]);

            // Obtener el ID de la devolución y el código del bono generado
            $result = $stmt->fetch(PDO::FETCH_ASSOC);
            $devolucion_id = $result['devolucion_id'] ?? null;
            $codigo_bono = $result['codigo_bono'] ?? null;

            Session::set('success', 'Devolución procesada exitosamente. ID: #' . $devolucion_id);
            Session::set('codigo_bono', $codigo_bono);
            
            header('Location: /devoluciones/detalle/' . $devolucion_id);
            exit;

        } catch (\Exception $e) {
            error_log("Error al procesar devolución: " . $e->getMessage());
            Session::set('error', 'Error al procesar devolución: ' . $e->getMessage());
            header('Location: /devoluciones/nueva');
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

        $title = 'Detalle de Devolución - Papelería JAKAKE';

        try {
            // Obtener información de la primera devolución (para datos generales)
            $stmt = $this->db->prepare("
                SELECT 
                    d.id,
                    d.venta_id,
                    d.motivo,
                    d.fecha_devolucion,
                    d.fecha_registro,
                    v.fecha as fecha_venta,
                    v.total as total_venta,
                    c.cedula as cliente_cedula,
                    c.nombre as cliente_nombre,
                    c.telefono as cliente_telefono,
                    u.nombre as usuario_nombre,
                    SUM(d.valor_devolucion) as monto_total
                FROM devoluciones d
                JOIN ventas v ON d.venta_id = v.id
                LEFT JOIN clientes c ON v.cliente_id = c.id
                JOIN usuarios u ON d.usuario_id = u.id
                WHERE d.id = ?
                GROUP BY d.id, d.venta_id, d.motivo, d.fecha_devolucion, d.fecha_registro,
                         v.fecha, v.total, c.cedula, c.nombre, c.telefono, u.nombre
            ");
            $stmt->execute([$id]);
            $devolucion = $stmt->fetch(PDO::FETCH_ASSOC);

            if (!$devolucion) {
                Session::set('error', 'Devolución no encontrada');
                header('Location: /devoluciones');
                exit;
            }

            // Obtener todos los productos de esta devolución
            $stmt = $this->db->prepare("
                SELECT 
                    d.producto_id,
                    d.cantidad,
                    d.valor_devolucion,
                    p.nombre as producto_nombre,
                    p.codigo as producto_codigo,
                    p.precio as precio_unitario
                FROM devoluciones d
                JOIN productos p ON d.producto_id = p.id
                WHERE d.id >= ? AND d.motivo = (SELECT motivo FROM devoluciones WHERE id = ?)
                  AND d.venta_id = (SELECT venta_id FROM devoluciones WHERE id = ?)
                  AND d.fecha_devolucion = (SELECT fecha_devolucion FROM devoluciones WHERE id = ?)
            ");
            $stmt->execute([$id, $id, $id, $id]);
            $detalles = $stmt->fetchAll(PDO::FETCH_ASSOC);

            // Obtener bono regalo asociado (si existe)
            $stmt = $this->db->prepare("
                SELECT * FROM bonos_regalo 
                WHERE devolucion_id = ?
            ");
            $stmt->execute([$id]);
            $bono = $stmt->fetch(PDO::FETCH_ASSOC);

        } catch (\PDOException $e) {
            error_log("Error al obtener detalle de devolución: " . $e->getMessage());
            Session::set('error', 'Error al obtener detalle de devolución');
            header('Location: /devoluciones');
            exit;
        }

        require_once __DIR__ . '/../views/devoluciones/detalle.php';
    }

    public function buscarVenta()
    {
        Session::start();
        if (!Session::get('user_id')) {
            header('Content-Type: application/json');
            echo json_encode(['error' => 'No autorizado']);
            exit;
        }

        $venta_id = intval($_GET['venta_id'] ?? 0);
        
        if (!$venta_id) {
            header('Content-Type: application/json');
            echo json_encode(['error' => 'ID de venta inválido']);
            exit;
        }

        try {
            $venta = $this->getVentaDetalle($venta_id);
            
            if (!$venta) {
                header('Content-Type: application/json');
                echo json_encode(['error' => 'Venta no encontrada']);
                exit;
            }

            // Verificar que la venta esté completada
            if ($venta['estado'] !== 'completada') {
                header('Content-Type: application/json');
                echo json_encode(['error' => 'Solo se pueden devolver ventas completadas']);
                exit;
            }

            $detalles = $this->getVentaProductos($venta_id);

            header('Content-Type: application/json');
            echo json_encode([
                'success' => true,
                'venta' => $venta,
                'productos' => $detalles
            ]);

        } catch (\PDOException $e) {
            error_log("Error al buscar venta: " . $e->getMessage());
            header('Content-Type: application/json');
            echo json_encode(['error' => 'Error al buscar venta']);
        }
        exit;
    }

    private function getVentaDetalle($venta_id)
    {
        $stmt = $this->db->prepare("
            SELECT 
                v.*,
                c.cedula as cliente_cedula,
                c.nombre as cliente_nombre,
                u.nombre as usuario_nombre
            FROM ventas v
            LEFT JOIN clientes c ON v.cliente_id = c.id
            JOIN usuarios u ON v.usuario_id = u.id
            WHERE v.id = ?
        ");
        $stmt->execute([$venta_id]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    private function getVentaProductos($venta_id)
    {
        $stmt = $this->db->prepare("
            SELECT 
                dv.*,
                p.nombre as producto_nombre,
                p.codigo as producto_codigo
            FROM detalle_venta dv
            JOIN productos p ON dv.producto_id = p.id
            WHERE dv.venta_id = ?
        ");
        $stmt->execute([$venta_id]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
}
