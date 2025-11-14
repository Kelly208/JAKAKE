<?php
namespace App\Config;

use PDO;

class Database
{
    private static $pdo = null;

    public static function getInstance(): PDO
    {
        if (self::$pdo === null) {
            $host = 'localhost';
            $db   = 'papeleria_jakake';
            $user = 'root';
            $pass = '';

            $dsn = "mysql:host=$host;dbname=$db;charset=utf8mb4";

            self::$pdo = new PDO($dsn, $user, $pass, [
                PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
                PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                PDO::ATTR_EMULATE_PREPARES   => false,
            ]);
        }
        return self::$pdo;
    }
}
