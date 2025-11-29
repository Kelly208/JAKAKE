<?php
namespace App\Controllers;

use App\Config\Database;
use App\Utils\Session;
use PDO;

class AuditoriaController
{
    private $db;

    public function __construct()
    {
        $this->db = Database::getInstance();
    }

    public function index()
    {
        Session::start();
        if (!Session::get('user_id') || !Session::isAdmin()) {
            header('Location: /dashboard');
            exit;
        }

        $title = 'Auditoría - Papelería JAKAKE';
        
        // Obtener filtros
        $tabla = $_GET['tabla'] ?? '';
        $operacion = $_GET['operacion'] ?? '';
        $fecha_desde = $_GET['fecha_desde'] ?? '';
        $fecha_hasta = $_GET['fecha_hasta'] ?? '';
        
        // Construir query con filtros
        $sql = "SELECT 
                    a.*,
                    u.nombre as usuario_nombre,
                    u.email as usuario_email
                FROM auditoria a
                LEFT JOIN usuarios u ON a.usuario_id = u.id
                WHERE 1=1";
        
        $params = [];
        
        if ($tabla) {
            $sql .= " AND a.tabla_afectada = ?";
            $params[] = $tabla;
        }
        
        if ($operacion) {
            $sql .= " AND a.operacion = ?";
            $params[] = $operacion;
        }
        
        if ($fecha_desde) {
            $sql .= " AND DATE(a.fecha_hora) >= ?";
            $params[] = $fecha_desde;
        }
        
        if ($fecha_hasta) {
            $sql .= " AND DATE(a.fecha_hora) <= ?";
            $params[] = $fecha_hasta;
        }
        
        $sql .= " ORDER BY a.fecha_hora DESC LIMIT 100";
        
        try {
            $stmt = $this->db->prepare($sql);
            $stmt->execute($params);
            $registros = $stmt->fetchAll(PDO::FETCH_ASSOC);
            
            // Obtener lista de tablas para filtro
            $stmt = $this->db->query("SELECT DISTINCT tabla_afectada FROM auditoria ORDER BY tabla_afectada");
            $tablas = $stmt->fetchAll(PDO::FETCH_COLUMN);
        } catch (\PDOException $e) {
            error_log("Error al obtener auditoría: " . $e->getMessage());
            $registros = [];
            $tablas = [];
        }

        require_once __DIR__ . '/../views/auditoria/index.php';
    }
}
