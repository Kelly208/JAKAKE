<?php
session_start();
$_SESSION['user_id'] = 1; // Simular usuario logueado

require_once __DIR__ . '/jakake-web/config/database.php';

use App\Config\Database;

$db = Database::getInstance();

$fecha_inicio = '2025-01-01';
$fecha_fin = '2025-11-29';

echo "=== TEST COMPLETO DEL REPORTE ===\n\n";

// Test 1: Resumen
echo "1. RESUMEN GENERAL:\n";
$stmt = $db->prepare("
    SELECT 
        COUNT(*) as total_ventas,
        COALESCE(SUM(total), 0) as monto_total,
        COALESCE(AVG(total), 0) as promedio_venta,
        COUNT(DISTINCT cliente_id) as clientes_unicos
    FROM ventas
    WHERE DATE(fecha) BETWEEN ? AND ?
    AND estado = 'completada'
");
$stmt->execute([$fecha_inicio, $fecha_fin]);
$resumen = $stmt->fetch(PDO::FETCH_ASSOC);
print_r($resumen);

// Test 2: Ventas por día
echo "\n2. VENTAS POR DÍA (primeras 5):\n";
$stmt = $db->prepare("
    SELECT 
        DATE(fecha) as fecha,
        COUNT(*) as cantidad_ventas,
        SUM(total) as monto_total
    FROM ventas
    WHERE DATE(fecha) BETWEEN ? AND ?
    AND estado = 'completada'
    GROUP BY DATE(fecha)
    ORDER BY fecha DESC
    LIMIT 5
");
$stmt->execute([$fecha_inicio, $fecha_fin]);
$ventas_diarias = $stmt->fetchAll(PDO::FETCH_ASSOC);
print_r($ventas_diarias);

// Test 3: Medios de pago
echo "\n3. VENTAS POR MEDIO DE PAGO:\n";
$stmt = $db->prepare("
    SELECT 
        medio_pago,
        COUNT(*) as cantidad,
        SUM(total) as monto_total
    FROM ventas
    WHERE DATE(fecha) BETWEEN ? AND ?
    AND estado = 'completada'
    GROUP BY medio_pago
    ORDER BY cantidad DESC
");
$stmt->execute([$fecha_inicio, $fecha_fin]);
$ventas_por_medio = $stmt->fetchAll(PDO::FETCH_ASSOC);
print_r($ventas_por_medio);

// Test 4: Top clientes
echo "\n4. TOP 5 CLIENTES:\n";
$stmt = $db->prepare("
    SELECT 
        c.nombre,
        c.cedula,
        COUNT(v.id) as total_compras,
        SUM(v.total) as monto_total
    FROM ventas v
    JOIN clientes c ON v.cliente_id = c.id
    WHERE DATE(v.fecha) BETWEEN ? AND ?
    AND v.estado = 'completada'
    GROUP BY v.cliente_id, c.nombre, c.cedula
    ORDER BY monto_total DESC
    LIMIT 5
");
$stmt->execute([$fecha_inicio, $fecha_fin]);
$top_clientes = $stmt->fetchAll(PDO::FETCH_ASSOC);
print_r($top_clientes);
