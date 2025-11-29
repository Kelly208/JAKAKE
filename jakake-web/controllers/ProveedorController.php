<?php
namespace App\Controllers;

use App\Config\Database;
use App\Utils\Session;
use App\Utils\Security;
use PDO;

class ProveedorController
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

        $title = 'Proveedores - Papelería JAKAKE';
        
        // Obtener todos los proveedores
        try {
            $stmt = $this->db->query("
                SELECT 
                    p.*,
                    (SELECT COUNT(*) FROM productos WHERE proveedor_id = p.id) as total_productos
                FROM proveedores p
                ORDER BY p.nombre ASC
            ");
            $proveedores = $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (\PDOException $e) {
            error_log("Error al obtener proveedores: " . $e->getMessage());
            $proveedores = [];
        }

        require_once __DIR__ . '/../views/proveedores/index.php';
    }

    public function crear()
    {
        Session::start();
        if (!Session::get('user_id')) {
            header('Location: /login');
            exit;
        }

        $title = 'Nuevo Proveedor - Papelería JAKAKE';

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            try {
                $nombre = Security::sanitize($_POST['nombre']);
                $nit = Security::sanitize($_POST['nit']);
                $telefono = Security::sanitize($_POST['telefono']);
                $email = Security::sanitize($_POST['email']);
                $direccion = Security::sanitize($_POST['direccion']);
                $contacto = Security::sanitize($_POST['contacto']);
                $estado = $_POST['estado'] ?? 'activo';

                // Validar NIT único
                $stmt = $this->db->prepare("SELECT id FROM proveedores WHERE nit = ?");
                $stmt->execute([$nit]);
                if ($stmt->fetch()) {
                    Session::set('error', 'El NIT ya está registrado');
                    header('Location: /proveedores/crear');
                    exit;
                }

                // Insertar proveedor
                $stmt = $this->db->prepare("
                    INSERT INTO proveedores (nombre, nit, telefono, email, direccion, contacto, estado)
                    VALUES (?, ?, ?, ?, ?, ?, ?)
                ");
                
                $stmt->execute([
                    $nombre,
                    $nit,
                    $telefono,
                    $email,
                    $direccion,
                    $contacto,
                    $estado
                ]);

                Session::set('success', 'Proveedor creado exitosamente');
                header('Location: /proveedores');
                exit;
            } catch (\PDOException $e) {
                error_log("Error al crear proveedor: " . $e->getMessage());
                Session::set('error', 'Error al crear proveedor: ' . $e->getMessage());
                header('Location: /proveedores/crear');
                exit;
            }
        }

        require_once __DIR__ . '/../views/proveedores/crear.php';
    }

    public function editar($id)
    {
        Session::start();
        if (!Session::get('user_id')) {
            header('Location: /login');
            exit;
        }

        $title = 'Editar Proveedor - Papelería JAKAKE';

        // Obtener datos del proveedor
        $stmt = $this->db->prepare("SELECT * FROM proveedores WHERE id = ?");
        $stmt->execute([$id]);
        $proveedor = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$proveedor) {
            Session::set('error', 'Proveedor no encontrado');
            header('Location: /proveedores');
            exit;
        }

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            try {
                $nombre = Security::sanitize($_POST['nombre']);
                $nit = Security::sanitize($_POST['nit']);
                $telefono = Security::sanitize($_POST['telefono']);
                $email = Security::sanitize($_POST['email']);
                $direccion = Security::sanitize($_POST['direccion']);
                $contacto = Security::sanitize($_POST['contacto']);
                $estado = $_POST['estado'] ?? 'activo';

                // Validar NIT único (excepto el actual)
                $stmt = $this->db->prepare("SELECT id FROM proveedores WHERE nit = ? AND id != ?");
                $stmt->execute([$nit, $id]);
                if ($stmt->fetch()) {
                    Session::set('error', 'El NIT ya está registrado por otro proveedor');
                    header("Location: /proveedores/editar/$id");
                    exit;
                }

                // Actualizar proveedor
                $stmt = $this->db->prepare("
                    UPDATE proveedores 
                    SET nombre = ?, nit = ?, telefono = ?, email = ?, 
                        direccion = ?, contacto = ?, estado = ?
                    WHERE id = ?
                ");
                
                $stmt->execute([
                    $nombre,
                    $nit,
                    $telefono,
                    $email,
                    $direccion,
                    $contacto,
                    $estado,
                    $id
                ]);

                Session::set('success', 'Proveedor actualizado exitosamente');
                header('Location: /proveedores');
                exit;
            } catch (\PDOException $e) {
                error_log("Error al actualizar proveedor: " . $e->getMessage());
                Session::set('error', 'Error al actualizar proveedor: ' . $e->getMessage());
                header("Location: /proveedores/editar/$id");
                exit;
            }
        }

        require_once __DIR__ . '/../views/proveedores/editar.php';
    }

    public function eliminar($id)
    {
        Session::start();
        if (!Session::get('user_id')) {
            header('Location: /login');
            exit;
        }

        try {
            // Verificar si tiene productos asociados
            $stmt = $this->db->prepare("SELECT COUNT(*) as total FROM productos WHERE proveedor_id = ?");
            $stmt->execute([$id]);
            $result = $stmt->fetch(PDO::FETCH_ASSOC);

            if ($result['total'] > 0) {
                Session::set('error', 'No se puede eliminar el proveedor porque tiene productos asociados');
            } else {
                $stmt = $this->db->prepare("DELETE FROM proveedores WHERE id = ?");
                $stmt->execute([$id]);
                Session::set('success', 'Proveedor eliminado exitosamente');
            }
        } catch (\PDOException $e) {
            error_log("Error al eliminar proveedor: " . $e->getMessage());
            Session::set('error', 'Error al eliminar proveedor');
        }

        header('Location: /proveedores');
        exit;
    }
}
