<?php
namespace App\Utils;

class Session
{
    public static function start(): void
    {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
    }

    public static function set(string $key, $value): void
    {
        $_SESSION[$key] = $value;
    }

    public static function get(string $key)
    {
        return $_SESSION[$key] ?? null;
    }

    public static function regenerate(): void
    {
        session_regenerate_id(true);
    }

    public static function destroy(): void
    {
        session_destroy();
    }

    public static function delete(string $key): void
    {
        if (isset($_SESSION[$key])) {
            unset($_SESSION[$key]);
        }
    }

    public static function isAdmin(): bool
    {
        return self::get('user_rol') === 'administrador';
    }

    public static function isCajero(): bool
    {
        return self::get('user_rol') === 'cajero';
    }

    public static function isAuthenticated(): bool
    {
        return self::get('user_id') !== null;
    }
}