<?php
namespace App\Controllers;

use App\Models\Usuario;
use App\Utils\Security;
use App\Utils\Session;

class AuthController {
    // Login
    public function login() {
        Session::start();
        
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $token = $_POST['csrf_token'] ?? '';
            if (!Security::validateToken($token)) {
                die("Token CSRF inválido");
            }
            
            $email = Security::sanitize($_POST['email']);
            $password = $_POST['password'];
            
            $usuarioModel = new Usuario();
            $user = $usuarioModel->login($email, $password);
            
            if ($user) {
                $userWithRoles = $usuarioModel->getUserWithRoles($user['usuario_id']);
                
                Session::set('user_id', $user['usuario_id']);
                Session::set('user_name', $user['nombre_completo']);
                Session::set('user_role', explode(',', $userWithRoles['roles'])[0]);
                
                header('Location: /dashboard');
                exit;
            } else {
                $error = "Credenciales inválidas";
            }
        }
        
        require __DIR__ . '/../views/auth/login.php';
    }

    // Logout
    public function logout() {
        Session::start();
        Session::destroy();
        header('Location: /login');
        exit;
    }

    // Registro de usuarios (solo admin)
    public function register() {
        Session::start();
        Session::requireLogin();
        Session::requireRole('Administrador');
        
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $data = [
                'nombre_completo' => Security::sanitize($_POST['nombre']),
                'identificacion' => Security::sanitize($_POST['identificacion']),
                'edad' => (int)$_POST['edad'],
                'telefono' => Security::sanitize($_POST['telefono']),
                'direccion' => Security::sanitize($_POST['direccion']),
                'email' => Security::sanitize($_POST['email']),
                'password' => $_POST['password']
            ];
            
            $usuarioModel = new Usuario();
            $usuarioModel->crearUsuario($data);
            
            // Asignar rol
            $usuario_id = $usuarioModel->pdo->lastInsertId();
            $rol_id = (int)$_POST['rol_id'];
            
            $sql = "INSERT INTO usuarios_roles (usuario_id, rol_id) VALUES (?, ?)";
            $stmt = $usuarioModel->pdo->prepare($sql);
            $stmt->execute([$usuario_id, $rol_id]);
            
            header('Location: /usuarios?success=1');
            exit;
        }
        
        // Obtener roles
        $pdo = \App\Config\Database::getInstance();
        $roles = $pdo->query("SELECT * FROM roles WHERE estado = 'activo'")->fetchAll();
        
        require __DIR__ . '/../views/usuarios/registrar.php';
    }
}