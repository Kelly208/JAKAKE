<?php
namespace App\Utils;

class Middleware
{
    /**
     * Verifica que el usuario esté autenticado
     */
    public static function requireAuth()
    {
        Session::start();
        if (!Session::get('user_id')) {
            Session::set('error', 'Debes iniciar sesión para acceder a esta página');
            header('Location: /login');
            exit;
        }
    }

    /**
     * Verifica que el usuario tenga rol de administrador
     */
    public static function requireAdmin()
    {
        self::requireAuth();
        
        if (Session::get('user_rol') !== 'administrador') {
            Session::set('error', 'Acceso denegado. Solo administradores pueden acceder a esta sección.');
            header('Location: /dashboard');
            exit;
        }
    }

    /**
     * Verifica que el usuario sea administrador o cajero
     */
    public static function requireRole($roles)
    {
        self::requireAuth();
        
        if (!is_array($roles)) {
            $roles = [$roles];
        }
        
        $userRole = Session::get('user_rol');
        
        if (!in_array($userRole, $roles)) {
            Session::set('error', 'No tienes permisos para acceder a esta sección.');
            header('Location: /dashboard');
            exit;
        }
    }

    /**
     * Verifica que la cuenta del usuario esté activa
     */
    public static function checkUserStatus()
    {
        self::requireAuth();
        
        $userId = Session::get('user_id');
        
        try {
            $db = \App\Config\Database::getInstance();
            $stmt = $db->prepare("SELECT estado FROM usuarios WHERE id = ?");
            $stmt->execute([$userId]);
            $user = $stmt->fetch(\PDO::FETCH_ASSOC);
            
            if (!$user || $user['estado'] !== 'activo') {
                Session::destroy();
                header('Location: /login?error=cuenta_inactiva');
                exit;
            }
        } catch (\PDOException $e) {
            error_log("Error al verificar estado de usuario: " . $e->getMessage());
        }
    }

    /**
     * Verifica si el usuario es administrador (retorna booleano)
     */
    public static function isAdmin()
    {
        Session::start();
        return Session::get('user_rol') === 'administrador';
    }

    /**
     * Verifica si el usuario es cajero (retorna booleano)
     */
    public static function isCajero()
    {
        Session::start();
        return Session::get('user_rol') === 'cajero';
    }

    /**
     * Verifica si el usuario tiene permiso para editar
     * (administradores pueden editar todo, cajeros solo pueden crear)
     */
    public static function canEdit()
    {
        return self::isAdmin();
    }

    /**
     * Verifica si el usuario puede eliminar registros
     */
    public static function canDelete()
    {
        return self::isAdmin();
    }

    /**
     * Verifica si el usuario puede ver reportes completos
     */
    public static function canViewReports()
    {
        Session::start();
        $rol = Session::get('user_rol');
        return in_array($rol, ['administrador', 'cajero']);
    }

    /**
     * Verifica si el usuario puede gestionar otros usuarios
     */
    public static function canManageUsers()
    {
        return self::isAdmin();
    }
}
