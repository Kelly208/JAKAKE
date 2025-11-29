<?php
$conn = new PDO('mysql:host=localhost;dbname=papeleria_jakake;charset=utf8mb4', 'root', '2409');

// Simular lo que hace el controller
$fecha_inicio = $_GET['fecha_inicio'] ?? date('Y-01-01');
$fecha_fin = $_GET['fecha_fin'] ?? date('Y-m-d');

echo "Fecha inicio: $fecha_inicio\n";
echo "Fecha fin: $fecha_fin\n\n";

echo "=== QUERY DE PRUEBA ===\n";
$stmt = $conn->prepare("
    SELECT 
        COUNT(*) as total_ventas,
        COALESCE(SUM(total), 0) as monto_total
    FROM ventas
    WHERE DATE(fecha) BETWEEN ? AND ?
    AND estado = 'completada'
");
$stmt->execute([$fecha_inicio, $fecha_fin]);
$resumen = $stmt->fetch();
echo "Total ventas encontradas: " . $resumen['total_ventas'] . "\n";
echo "Monto total: $" . $resumen['monto_total'] . "\n\n";

echo "=== VERIFICAR FECHAS EN VENTAS ===\n";
$stmt = $conn->query("SELECT MIN(DATE(fecha)) as min, MAX(DATE(fecha)) as max FROM ventas");
$fechas = $stmt->fetch();
echo "Fecha más antigua: " . $fechas['min'] . "\n";
echo "Fecha más reciente: " . $fechas['max'] . "\n";
