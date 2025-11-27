<?php
namespace App\Controllers;

use App\Config\Database;
use App\Utils\Session;
use App\Utils\Security;
use PDO;

class AuthController
{
    private $db;

    public function __construct()
    {
        $this->db = Database::getInstance();
    }

    public function showLogin()
    {
        Session::start();
        
        // Si ya está autenticado, redirigir al dashboard
        if (Session::get('user_id')) {
            header('Location: /dashboard');
            exit;
        }

        // Mostrar vista de login
        require_once __DIR__ . '/../views/auth/login.php';
    }

    public function login()
    {
        Session::start();

        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $this->showLogin();
            return;
        }

        // Validar CSRF token
        if (!isset($_POST['csrf_token']) || !Security::validateToken($_POST['csrf_token'])) {
            $error = 'Token de seguridad inválido';
            require_once __DIR__ . '/../views/auth/login.php';
            return;
        }

        $email = $_POST['email'] ?? '';
        $password = $_POST['password'] ?? '';

        // Validar campos
        if (empty($email) || empty($password)) {
            $error = 'Por favor ingresa email y contraseña';
            require_once __DIR__ . '/../views/auth/login.php';
            return;
        }

        // Buscar usuario en la base de datos
        try {
            $stmt = $this->db->prepare("
                SELECT id, nombre, email, password_hash, rol, estado 
                FROM usuarios 
                WHERE email = ? AND estado = 'activo'
                LIMIT 1
            ");
            $stmt->execute([$email]);
            $user = $stmt->fetch(PDO::FETCH_ASSOC);

            // Verificar si existe el usuario
            if (!$user) {
                $error = 'Credenciales incorrectas';
                require_once __DIR__ . '/../views/auth/login.php';
                return;
            }

            // Verificar contraseña
            if (!password_verify($password, $user['password_hash'])) {
                $error = 'Credenciales incorrectas';
                require_once __DIR__ . '/../views/auth/login.php';
                return;
            }

            // Login exitoso - crear sesión
            Session::set('user_id', $user['id']);
            Session::set('user_name', $user['nombre']);
            Session::set('user_email', $user['email']);
            Session::set('user_rol', $user['rol']);
            Session::regenerate();

            // Registrar en auditoría (opcional)
            $this->registrarLoginAuditoria($user['id']);

            // Redirigir al dashboard
            header('Location: /dashboard');
            exit;

        } catch (\PDOException $e) {
            error_log("Error en login: " . $e->getMessage());
            $error = 'Error al procesar el login. Intenta nuevamente.';
            require_once __DIR__ . '/../views/auth/login.php';
        }
    }

    public function logout()
    {
        Session::start();
        Session::destroy();
        header('Location: /login');
        exit;
    }

    private function registrarLoginAuditoria($userId)
    {
        try {
            $stmt = $this->db->prepare("
                INSERT INTO auditoria (tabla_afectada, operacion, registro_id, usuario_id, valores_nuevos, ip_usuario)
                VALUES ('usuarios', 'LOGIN', ?, ?, JSON_OBJECT('accion', 'login_exitoso'), ?)
            ");
            $stmt->execute([
                $userId,
                $userId,
                $_SERVER['REMOTE_ADDR'] ?? '0.0.0.0'
            ]);
        } catch (\PDOException $e) {
            error_log("Error al registrar auditoría: " . $e->getMessage());
        }
    }
}
