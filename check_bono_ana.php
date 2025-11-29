<?php
$conn = new PDO('mysql:host=localhost;dbname=papeleria_jakake;charset=utf8mb4', 'root', '2409');
$conn->exec("SET NAMES utf8mb4");

echo "=== BONOS DE ANA MARIA LOPEZ ===\n\n";

// Buscar cliente
$stmt = $conn->query("SELECT id, nombre, cedula FROM clientes WHERE nombre LIKE '%Ana Maria%'");
$cliente = $stmt->fetch(PDO::FETCH_ASSOC);

if ($cliente) {
    echo "Cliente encontrado:\n";
    echo "ID: {$cliente['id']}\n";
    echo "Nombre: {$cliente['nombre']}\n";
    echo "Cédula: {$cliente['cedula']}\n\n";
    
    // Buscar bonos
    echo "Bonos de este cliente:\n";
    $stmt = $conn->prepare("
        SELECT 
            id,
            codigo,
            valor,
            estado,
            fecha_vencimiento,
            fecha_uso,
            venta_uso_id
        FROM bonos_regalo 
        WHERE cliente_id = ?
        ORDER BY id DESC
    ");
    $stmt->execute([$cliente['id']]);
    $bonos = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    if (empty($bonos)) {
        echo "No tiene bonos\n";
    } else {
        foreach ($bonos as $bono) {
            echo "- ID: {$bono['id']}\n";
            echo "  Código: {$bono['codigo']}\n";
            echo "  Valor: \${$bono['valor']}\n";
            echo "  Estado: {$bono['estado']}\n";
            echo "  Vence: {$bono['fecha_vencimiento']}\n";
            if ($bono['estado'] == 'usado') {
                echo "  Usado en venta: #{$bono['venta_uso_id']} el {$bono['fecha_uso']}\n";
            }
            echo "\n";
        }
    }
} else {
    echo "Cliente no encontrado\n";
}

echo "\n=== VERIFICAR CÓDIGO BONO-20251128-000002 ===\n";
$stmt = $conn->query("
    SELECT 
        bg.id,
        bg.codigo,
        bg.cliente_id,
        c.nombre as cliente_nombre,
        bg.valor,
        bg.estado,
        bg.fecha_vencimiento
    FROM bonos_regalo bg
    JOIN clientes c ON bg.cliente_id = c.id
    WHERE bg.codigo = 'BONO-20251128-000002'
");
$bono = $stmt->fetch(PDO::FETCH_ASSOC);

if ($bono) {
    echo "Bono encontrado:\n";
    print_r($bono);
} else {
    echo "Bono no encontrado con ese código\n";
}
