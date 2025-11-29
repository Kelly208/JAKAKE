<?php
namespace App\Controllers;

use App\Config\Database;
use App\Utils\Session;
use App\Utils\Security;
use PDO;

class UsuarioController
{
    private $db;

    public function __construct()
    {
        $this->db = Database::getInstance();
    }

    public function index()
    {

        $title = 'Gestión de Usuarios - Papelería JAKAKE';
        
        try {
            $stmt = $this->db->query("
                SELECT 
                    u.*,
                    (SELECT COUNT(*) FROM ventas WHERE usuario_id = u.id) as total_ventas
                FROM usuarios u
                ORDER BY u.fecha_registro DESC
            ");
            $usuarios = $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (\PDOException $e) {
            error_log("Error al obtener usuarios: " . $e->getMessage());
            $usuarios = [];
        }

        require_once __DIR__ . '/../views/usuarios/index.php';
    }

    public function crear()
    {

        $title = 'Nuevo Usuario - Papelería JAKAKE';

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $this->procesarCrear();
            return;
        }

        require_once __DIR__ . '/../views/usuarios/crear.php';
    }

    private function procesarCrear()
    {
        try {
            $nombre = Security::sanitize($_POST['nombre']);
            $email = Security::sanitize($_POST['email']);
            $password = $_POST['password'];
            $rol = Security::sanitize($_POST['rol']);

            // Validar que el rol sea válido
            if (!in_array($rol, ['administrador', 'cajero'])) {
                throw new \Exception("Rol inválido");
            }

            // Verificar si el email ya existe
            $stmt = $this->db->prepare("SELECT id FROM usuarios WHERE email = ?");
            $stmt->execute([$email]);
            if ($stmt->fetch()) {
                throw new \Exception("Ya existe un usuario con este email");
            }

            // Hash del password
            $password_hash = password_hash($password, PASSWORD_DEFAULT);

            // Insertar usuario
            $stmt = $this->db->prepare("
                INSERT INTO usuarios (nombre, email, password_hash, rol, estado)
                VALUES (?, ?, ?, ?, 'activo')
            ");
            
            $stmt->execute([$nombre, $email, $password_hash, $rol]);

            Session::set('success', 'Usuario creado exitosamente');
            header('Location: /usuarios');
            exit;

        } catch (\Exception $e) {
            error_log("Error al crear usuario: " . $e->getMessage());
            Session::set('error', 'Error al crear usuario: ' . $e->getMessage());
            header('Location: /usuarios/crear');
            exit;
        }
    }

    public function editar($id)
    {

        $title = 'Editar Usuario - Papelería JAKAKE';

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $this->procesarEditar($id);
            return;
        }

        try {
            $stmt = $this->db->prepare("SELECT * FROM usuarios WHERE id = ?");
            $stmt->execute([$id]);
            $usuario = $stmt->fetch(PDO::FETCH_ASSOC);

            if (!$usuario) {
                Session::set('error', 'Usuario no encontrado');
                header('Location: /usuarios');
                exit;
            }

            // Obtener estadísticas del usuario
            $stmt = $this->db->prepare("
                SELECT 
                    COUNT(*) as total_ventas,
                    COALESCE(SUM(total), 0) as monto_total_ventas
                FROM ventas
                WHERE usuario_id = ? AND estado = 'completada'
            ");
            $stmt->execute([$id]);
            $estadisticas = $stmt->fetch(PDO::FETCH_ASSOC);

        } catch (\PDOException $e) {
            error_log("Error al obtener usuario: " . $e->getMessage());
            Session::set('error', 'Error al obtener usuario');
            header('Location: /usuarios');
            exit;
        }

        require_once __DIR__ . '/../views/usuarios/editar.php';
    }

    private function procesarEditar($id)
    {
        try {
            $nombre = Security::sanitize($_POST['nombre']);
            $email = Security::sanitize($_POST['email']);
            $rol = Security::sanitize($_POST['rol']);
            $estado = Security::sanitize($_POST['estado']);

            // Validar que el rol sea válido
            if (!in_array($rol, ['administrador', 'cajero'])) {
                throw new \Exception("Rol inválido");
            }

            // Verificar que no se esté editando el propio usuario para cambiar su rol o desactivarlo
            if (Session::get('user_id') == $id) {
                if (Session::get('user_rol') !== $rol) {
                    throw new \Exception("No puedes cambiar tu propio rol");
                }
                if ($estado !== 'activo') {
                    throw new \Exception("No puedes desactivar tu propia cuenta");
                }
            }

            // Verificar si el email ya existe en otro usuario
            $stmt = $this->db->prepare("SELECT id FROM usuarios WHERE email = ? AND id != ?");
            $stmt->execute([$email, $id]);
            if ($stmt->fetch()) {
                throw new \Exception("Ya existe otro usuario con este email");
            }

            $stmt = $this->db->prepare("
                UPDATE usuarios 
                SET nombre = ?, email = ?, rol = ?, estado = ?
                WHERE id = ?
            ");
            
            $stmt->execute([$nombre, $email, $rol, $estado, $id]);

            Session::set('success', 'Usuario actualizado exitosamente');
            header('Location: /usuarios');
            exit;

        } catch (\Exception $e) {
            error_log("Error al actualizar usuario: " . $e->getMessage());
            Session::set('error', 'Error al actualizar usuario: ' . $e->getMessage());
            header('Location: /usuarios/editar/' . $id);
            exit;
        }
    }

    public function resetPassword($id)
    {

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            try {
                $nueva_password = $_POST['nueva_password'];
                $confirmar_password = $_POST['confirmar_password'];

                if ($nueva_password !== $confirmar_password) {
                    throw new \Exception("Las contraseñas no coinciden");
                }

                if (strlen($nueva_password) < 6) {
                    throw new \Exception("La contraseña debe tener al menos 6 caracteres");
                }

                $password_hash = password_hash($nueva_password, PASSWORD_DEFAULT);

                $stmt = $this->db->prepare("
                    UPDATE usuarios 
                    SET password = ?
                    WHERE id = ?
                ");
                
                $stmt->execute([$password_hash, $id]);

                Session::set('success', 'Contraseña actualizada exitosamente');
                header('Location: /usuarios');
                exit;

            } catch (\Exception $e) {
                error_log("Error al resetear contraseña: " . $e->getMessage());
                Session::set('error', 'Error: ' . $e->getMessage());
                header('Location: /usuarios/editar/' . $id);
                exit;
            }
        }
    }
}
