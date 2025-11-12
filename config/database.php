<?php
namespace App\Config;

class Database {
    private static $instance = null;
    private $pdo;

    private function __construct() {
        $host = 'localhost';
        $db = 'papeleria_jakake';
        $user = 'root';
        $pass = ''; // Cambiar en producción

        try {
            $this->pdo = new \PDO(
                "mysql:host=$host;dbname=$db;charset=utf8mb4",
                $user,
                $pass,
                [
                    \PDO::ATTR_ERRMODE => \PDO::ERRMODE_EXCEPTION,
                    \PDO::ATTR_DEFAULT_FETCH_MODE => \PDO::FETCH_ASSOC,
                    \PDO::ATTR_EMULATE_PREPARES => false,
                ]
            );
        } catch (\PDOException $e) {
            die("Error de conexión: " . $e->getMessage());
        }
    }

    public static function getInstance() {
        if (self::$instance === null) {
            self::$instance = new self();
        }
        return self::$instance->pdo;
    }

    public static function beginTransaction() {
        self::getInstance()->beginTransaction();
    }

    public static function commit() {
        self::getInstance()->commit();
    }

    public static function rollBack() {
        self::getInstance()->rollBack();
    }
}