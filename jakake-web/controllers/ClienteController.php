<?php
namespace App\Controllers;

use App\Config\Database;
use App\Utils\Session;
use App\Utils\Security;
use PDO;

class ClienteController
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

        $title = 'Clientes - Papelería JAKAKE';
        
        // Obtener todos los clientes con información de políticas
        try {
            $stmt = $this->db->query("
                SELECT 
                    c.*,
                    ap.fecha_aceptacion,
                    ap.version_politica,
                    (SELECT COUNT(*) FROM ventas WHERE cliente_id = c.id) as total_compras
                FROM clientes c
                LEFT JOIN aceptacion_politicas ap ON c.id = ap.cliente_id
                ORDER BY c.fecha_registro DESC
            ");
            $clientes = $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (\PDOException $e) {
            error_log("Error al obtener clientes: " . $e->getMessage());
            $clientes = [];
        }

        require_once __DIR__ . '/../views/clientes/index.php';
    }

    public function crear()
    {
        Session::start();
        if (!Session::get('user_id')) {
            header('Location: /login');
            exit;
        }

        $title = 'Nuevo Cliente - Papelería JAKAKE';

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $this->procesarCrear();
            return;
        }

        require_once __DIR__ . '/../views/clientes/crear.php';
    }

    private function procesarCrear()
    {
        try {
            $this->db->beginTransaction();

            $cedula = Security::sanitize($_POST['cedula']);
            $nombre = Security::sanitize($_POST['nombre']);
            $apellido = Security::sanitize($_POST['apellido']);
            $direccion = Security::sanitize($_POST['direccion'] ?? '');
            $telefono = Security::sanitize($_POST['telefono'] ?? '');
            $email = Security::sanitize($_POST['email']);
            $acepta_politicas = isset($_POST['acepta_politicas']) ? 1 : 0;

            // Verificar si la cédula ya existe
            $stmt = $this->db->prepare("SELECT id FROM clientes WHERE cedula = ?");
            $stmt->execute([$cedula]);
            if ($stmt->fetch()) {
                throw new \Exception("Ya existe un cliente con esta cédula");
            }

            // Insertar cliente
            $stmt = $this->db->prepare("
                INSERT INTO clientes 
                (cedula, nombre, apellido, direccion, telefono, email, estado)
                VALUES (?, ?, ?, ?, ?, ?, 'activo')
            ");
            
            $stmt->execute([
                $cedula, $nombre, $apellido, $direccion, $telefono, $email
            ]);

            $cliente_id = $this->db->lastInsertId();

            // Registrar aceptación de políticas si fue marcado
            if ($acepta_politicas) {
                $stmt = $this->db->prepare("
                    INSERT INTO aceptacion_politicas 
                    (cliente_id, version_politica, ip_aceptacion)
                    VALUES (?, 'v1.0', ?)
                ");
                $stmt->execute([$cliente_id, $_SERVER['REMOTE_ADDR']]);
            }

            $this->db->commit();

            Session::set('success', 'Cliente registrado exitosamente');
            header('Location: /clientes');
            exit;

        } catch (\Exception $e) {
            $this->db->rollBack();
            error_log("Error al crear cliente: " . $e->getMessage());
            Session::set('error', 'Error al crear cliente: ' . $e->getMessage());
            header('Location: /clientes/crear');
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

        $title = 'Editar Cliente - Papelería JAKAKE';

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $this->procesarEditar($id);
            return;
        }

        // Obtener cliente con información de políticas
        try {
            $stmt = $this->db->prepare("
                SELECT 
                    c.*,
                    ap.fecha_aceptacion,
                    ap.version_politica
                FROM clientes c
                LEFT JOIN aceptacion_politicas ap ON c.id = ap.cliente_id
                WHERE c.id = ?
            ");
            $stmt->execute([$id]);
            $cliente = $stmt->fetch(PDO::FETCH_ASSOC);

            if (!$cliente) {
                Session::set('error', 'Cliente no encontrado');
                header('Location: /clientes');
                exit;
            }

            // Obtener historial de compras usando procedimiento almacenado
            $stmt = $this->db->prepare("CALL sp_historial_compras_cliente(?)");
            $stmt->execute([$id]);
            $historial = $stmt->fetchAll(PDO::FETCH_ASSOC);

        } catch (\PDOException $e) {
            error_log("Error al obtener cliente: " . $e->getMessage());
            Session::set('error', 'Error al obtener cliente');
            header('Location: /clientes');
            exit;
        }

        require_once __DIR__ . '/../views/clientes/editar.php';
    }

    private function procesarEditar($id)
    {
        try {
            $nombre = Security::sanitize($_POST['nombre']);
            $apellido = Security::sanitize($_POST['apellido']);
            $direccion = Security::sanitize($_POST['direccion'] ?? '');
            $telefono = Security::sanitize($_POST['telefono'] ?? '');
            $email = Security::sanitize($_POST['email']);
            $estado = Security::sanitize($_POST['estado']);

            $stmt = $this->db->prepare("
                UPDATE clientes 
                SET nombre = ?, apellido = ?, direccion = ?, telefono = ?,
                    email = ?, estado = ?
                WHERE id = ?
            ");
            
            $stmt->execute([
                $nombre, $apellido, $direccion, $telefono, $email, $estado, $id
            ]);

            Session::set('success', 'Cliente actualizado exitosamente');
            header('Location: /clientes');
            exit;

        } catch (\PDOException $e) {
            error_log("Error al actualizar cliente: " . $e->getMessage());
            Session::set('error', 'Error al actualizar cliente');
            header('Location: /clientes/editar/' . $id);
            exit;
        }
    }

    public function historial($id)
    {
        Session::start();
        if (!Session::get('user_id')) {
            header('Location: /login');
            exit;
        }

        try {
            // Obtener información del cliente
            $stmt = $this->db->prepare("SELECT * FROM clientes WHERE id = ?");
            $stmt->execute([$id]);
            $cliente = $stmt->fetch(PDO::FETCH_ASSOC);

            if (!$cliente) {
                Session::set('error', 'Cliente no encontrado');
                header('Location: /clientes');
                exit;
            }

            // Obtener historial usando procedimiento
            $stmt = $this->db->prepare("CALL sp_historial_compras_cliente(?)");
            $stmt->execute([$id]);
            $historial = $stmt->fetchAll(PDO::FETCH_ASSOC);

            $title = 'Historial de Compras - ' . $cliente['nombre'] . ' ' . $cliente['apellido'];

            require_once __DIR__ . '/../views/clientes/historial.php';

        } catch (\PDOException $e) {
            error_log("Error al obtener historial: " . $e->getMessage());
            Session::set('error', 'Error al obtener historial');
            header('Location: /clientes');
            exit;
        }
    }
}
