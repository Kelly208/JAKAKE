<?php
$conn = new PDO('mysql:host=localhost;dbname=papeleria_jakake;charset=utf8mb4', 'root', '2409');

echo "=== VERIFICANDO VENTAS ===\n";
$stmt = $conn->query("SELECT COUNT(*) as total FROM ventas");
$result = $stmt->fetch();
echo "Total ventas en BD: " . $result['total'] . "\n\n";

echo "=== VENTAS CON FECHAS ===\n";
$stmt = $conn->query("SELECT id, fecha, total, estado FROM ventas ORDER BY fecha DESC LIMIT 5");
while($row = $stmt->fetch()) {
    echo "ID: " . $row['id'] . " | Fecha: " . $row['fecha'] . " | Total: $" . $row['total'] . " | Estado: " . $row['estado'] . "\n";
}

echo "\n=== REPORTE QUERY ===\n";
$fecha_inicio = '2025-01-01';
$fecha_fin = '2025-11-29';
$stmt = $conn->prepare("
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
$resumen = $stmt->fetch();
echo "Período: $fecha_inicio a $fecha_fin\n";
echo "Total ventas: " . $resumen['total_ventas'] . "\n";
echo "Monto total: $" . $resumen['monto_total'] . "\n";
