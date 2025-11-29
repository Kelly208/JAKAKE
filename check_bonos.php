<?php
$conn = new PDO('mysql:host=localhost;dbname=papeleria_jakake;charset=utf8mb4', 'root', '2409');
$conn->exec("SET NAMES utf8mb4");

echo "=== BONOS DISPONIBLES ===\n\n";
$stmt = $conn->query("
    SELECT 
        bg.id,
        bg.codigo,
        bg.cliente_id,
        c.nombre as cliente_nombre,
        bg.valor,
        bg.estado,
        bg.fecha_vencimiento,
        LENGTH(bg.codigo) as longitud_codigo,
        HEX(bg.codigo) as hex_codigo
    FROM bonos_regalo bg
    JOIN clientes c ON bg.cliente_id = c.id
    ORDER BY bg.id DESC
    LIMIT 5
");

while ($bono = $stmt->fetch(PDO::FETCH_ASSOC)) {
    echo "ID: {$bono['id']}\n";
    echo "Código: [{$bono['codigo']}]\n";
    echo "Longitud: {$bono['longitud_codigo']} caracteres\n";
    echo "HEX: {$bono['hex_codigo']}\n";
    echo "Cliente: {$bono['cliente_nombre']} (ID: {$bono['cliente_id']})\n";
    echo "Valor: \${$bono['valor']}\n";
    echo "Estado: {$bono['estado']}\n";
    echo "Vence: {$bono['fecha_vencimiento']}\n";
    echo str_repeat("-", 50) . "\n\n";
}
