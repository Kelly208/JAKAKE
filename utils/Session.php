<?php
namespace App\Utils;

class Session {
    public static function start() {
        if (session_status() === PHP_SESSION_NONE) {
            ini_set('session.cookie_httponly', 1);
            ini_set('session.use_only_cookies', 1);
            ini_set('session.cookie_secure', 0); // Cambiar a 1 si usas HTTPS
            session_start();
        }
    }

    public static function set($key, $value) {
        $_SESSION[$key] = $value;
    }

    public static function get($key) {
        return $_SESSION[$key] ?? null;
    }

    public static function destroy() {
        session_destroy();
    }

    public static function requireLogin() {
        self::start();
        if (!self::get('user_id')) {
            header('Location: /login');
            exit;
        }
    }

    public static function requireRole($roles) {
        self::start();
        $userRole = self::get('user_role');
        if (!in_array($userRole, (array)$roles)) {
            http_response_code(403);
            die("Acceso denegado. Rol requerido: " . implode(', ', (array)$roles));
        }
    }
}