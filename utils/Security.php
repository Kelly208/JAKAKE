<?php
namespace App\Utils;

class Security {
    // Generar hash de contraseña
    public static function hashPassword($password) {
        return password_hash($password, PASSWORD_BCRYPT, ['cost' => 12]);
    }

    // Verificar contraseña
    public static function verifyPassword($password, $hash) {
        return password_verify($password, $hash);
    }

    // Sanitizar entrada (XSS)
    public static function sanitize($data) {
        if (is_array($data)) {
            return array_map([self::class, 'sanitize'], $data);
        }
        return htmlspecialchars(trim($data), ENT_QUOTES, 'UTF-8');
    }

    // Generar token CSRF
    public static function generateToken() {
        if (empty($_SESSION['csrf_token'])) {
            $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
        }
        return $_SESSION['csrf_token'];
    }

    // Validar token CSRF
    public static function validateToken($token) {
        return hash_equals($_SESSION['csrf_token'] ?? '', $token);
    }

    // Generar salt aleatorio
    public static function generateSalt() {
        return bin2hex(random_bytes(16));
    }
}