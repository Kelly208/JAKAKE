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
                    u.nombre as cajero_nombre,
                    (SELECT COUNT(*) FROM detalle_venta WHERE venta_id = v.id) as total_productos
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
            $bono_id = intval($_POST['bono_id'] ?? 0);
            
            // Log incoming data for debugging
            error_log("=== PROCESANDO VENTA ===");
            error_log("Cliente ID: " . $cliente_id);
            error_log("Medio Pago: " . $medio_pago);
            error_log("Bono ID: " . $bono_id);
            error_log("Productos JSON raw: " . $productos_json);
            
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
                    'precio_unitario' => floatval($prod['precio'])
                ];
            }

            $productos_json_final = json_encode($productos_array);
            $usuario_id = Session::get('user_id');
            
            error_log("Productos JSON final: " . $productos_json_final);
            error_log("Usuario ID: " . $usuario_id);

            // Llamar al procedimiento almacenado sp_registrar_venta_completa
            $stmt = $this->db->prepare("
                CALL sp_registrar_venta_completa(?, ?, ?, ?, @venta_id, @total_final)
            ");
            
            $stmt->execute([
                $cliente_id,
                $usuario_id,
                $medio_pago,
                $productos_json_final
            ]);

            // Obtener los valores de salida del procedimiento
            $result = $this->db->query("SELECT @venta_id as venta_id, @total_final as total_final")->fetch(PDO::FETCH_ASSOC);
            $venta_id = $result['venta_id'] ?? null;
            
            error_log("Venta creada exitosamente. ID: " . $venta_id);

            // Si hay un bono aplicado, marcarlo como usado
            if ($bono_id > 0) {
                $stmt = $this->db->prepare("
                    UPDATE bonos_regalo 
                    SET estado = 'usado', 
                        fecha_uso = NOW(),
                        venta_uso_id = ?
                    WHERE id = ? AND cliente_id = ? AND estado = 'activo'
                ");
                $stmt->execute([$venta_id, $bono_id, $cliente_id]);
                error_log("Bono #$bono_id marcado como usado");
            }

            Session::set('success', 'Venta #' . $venta_id . ' registrada exitosamente.');
            // Redirigir de vuelta a nueva venta para permitir ventas consecutivas
            header('Location: /ventas/nueva');
            exit;

        } catch (\Exception $e) {
            error_log("Error al procesar venta: " . $e->getMessage());
            error_log("Stack trace: " . $e->getTraceAsString());
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
                FROM detalle_venta dv
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
                SELECT id, cedula, nombre
                FROM clientes 
                WHERE acepto_politicas = 1
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

    public function validarBono()
    {
        header('Content-Type: application/json');
        
        $codigo = trim($_GET['codigo'] ?? '');
        $cliente_id = intval($_GET['cliente_id'] ?? 0);

        error_log("=== VALIDANDO BONO ===");
        error_log("Código recibido: [$codigo]");
        error_log("Cliente ID: $cliente_id");

        if (empty($codigo) || !$cliente_id) {
            echo json_encode(['error' => 'Datos incompletos']);
            exit;
        }

        try {
            $stmt = $this->db->prepare("
                SELECT 
                    bg.*,
                    DATE_FORMAT(bg.fecha_vencimiento, '%d/%m/%Y') as fecha_vencimiento_format
                FROM bonos_regalo bg
                WHERE bg.codigo = ? 
                  AND bg.cliente_id = ?
                  AND bg.estado = 'activo'
                  AND bg.fecha_vencimiento >= CURDATE()
            ");
            $stmt->execute([$codigo, $cliente_id]);
            $bono = $stmt->fetch(PDO::FETCH_ASSOC);

            error_log("Bono encontrado: " . ($bono ? "SI (ID: {$bono['id']})" : "NO"));

            if ($bono) {
                echo json_encode([
                    'success' => true,
                    'bono' => [
                        'id' => $bono['id'],
                        'codigo' => $bono['codigo'],
                        'valor' => $bono['valor'],
                        'fecha_vencimiento' => $bono['fecha_vencimiento_format']
                    ]
                ]);
            } else {
                // Verificar por qué falló
                $stmt = $this->db->prepare("SELECT id, codigo, cliente_id, estado, fecha_vencimiento FROM bonos_regalo WHERE codigo = ?");
                $stmt->execute([$codigo]);
                $bonoDebug = $stmt->fetch(PDO::FETCH_ASSOC);
                
                if (!$bonoDebug) {
                    $mensaje = 'El código del bono no existe';
                } elseif ($bonoDebug['cliente_id'] != $cliente_id) {
                    $mensaje = 'Este bono no pertenece al cliente seleccionado';
                } elseif ($bonoDebug['estado'] != 'activo') {
                    $mensaje = 'Este bono ya fue utilizado';
                } else {
                    $mensaje = 'Este bono está vencido';
                }
                
                error_log("Razón del rechazo: $mensaje");
                
                echo json_encode(['error' => $mensaje]);
            }
        } catch (\PDOException $e) {
            error_log("Error al validar bono: " . $e->getMessage());
            echo json_encode(['error' => 'Error al validar el bono: ' . $e->getMessage()]);
        }
        exit;
    }
}
