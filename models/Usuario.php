<?php
namespace App\Models;

use App\Utils\Security;

class Usuario extends BaseModel {
    protected $table = 'usuarios';
    protected $primaryKey = 'usuario_id';

    // Login con auditoría
    public function login($email, $password) {
        $sql = "SELECT * FROM usuarios WHERE email = ? AND estado = 'activo'";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([$email]);
        $user = $stmt->fetch();

        if ($user && Security::verifyPassword($password, $user['hash_contrasena'])) {
            // Registrar acceso en auditoría
            $this->registrarAuditoria('login_exitoso', $user['usuario_id']);
            return $user;
        }
        
        if ($user) {
            $this->registrarAuditoria('login_fallido', $user['usuario_id']);
        }
        
        return false;
    }

    // Crear usuario con hash de contraseña
    public function crearUsuario($data) {
        $data['hash_contrasena'] = Security::hashPassword($data['password']);
        $data['salt'] = Security::generateSalt();
        unset($data['password']);
        
        return $this->create($data);
    }

    // Validar aceptación de políticas
    public function registrarConsentimiento($usuario_id, $politica_id, $aceptacion, $ip) {
        $sql = "INSERT INTO consentimientos 
                (usuario_id, politica_id, aceptacion, ip_address, user_agent) 
                VALUES (?, ?, ?, ?, ?)";
        $stmt = $this->pdo->prepare($sql);
        return $stmt->execute([
            $usuario_id,
            $politica_id,
            $aceptacion,
            $ip,
            $_SERVER['HTTP_USER_AGENT'] ?? null
        ]);
    }

    private function registrarAuditoria($accion, $usuario_id) {
        $sql = "INSERT INTO auditoria 
                (tabla_afectada, registro_id, operacion, usuario_id, ip_address) 
                VALUES ('usuarios', ?, ?, ?, ?)";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([$usuario_id, $accion, $usuario_id, $_SERVER['REMOTE_ADDR']]);
    }

    // Obtener usuario con roles
    public function getUserWithRoles($usuario_id) {
        $sql = "SELECT u.*, GROUP_CONCAT(r.nombre_rol) AS roles
                FROM usuarios u
                LEFT JOIN usuarios_roles ur ON u.usuario_id = ur.usuario_id
                LEFT JOIN roles r ON ur.rol_id = r.rol_id
                WHERE u.usuario_id = ?";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([$usuario_id]);
        return $stmt->fetch();
    }
}