<?php
$conn = new PDO('mysql:host=localhost;dbname=papeleria_jakake;charset=utf8mb4', 'root', '2409');

echo "=== TABLA DEVOLUCIONES ===\n";
$stmt = $conn->query('DESCRIBE devoluciones');
while($row = $stmt->fetch()) {
    echo $row['Field'] . " (" . $row['Type'] . ")\n";
}

echo "\n=== TABLA BONOS_REGALO ===\n";
$stmt = $conn->query('DESCRIBE bonos_regalo');
while($row = $stmt->fetch()) {
    echo $row['Field'] . " (" . $row['Type'] . ")\n";
}

echo "\n=== TEST CLIENTES ===\n";
$stmt = $conn->query("SELECT id, cedula, nombre FROM clientes WHERE acepto_politicas = 1 LIMIT 5");
echo "Total clientes que aceptaron políticas: ";
$clientes = $stmt->fetchAll();
echo count($clientes) . "\n";
foreach($clientes as $c) {
    echo "- " . $c['nombre'] . " (" . $c['cedula'] . ")\n";
}

echo "\n=== TEST USUARIOS ===\n";
$stmt = $conn->query("SELECT id, nombre, email, rol FROM usuarios");
$usuarios = $stmt->fetchAll();
echo "Total usuarios: " . count($usuarios) . "\n";
foreach($usuarios as $u) {
    echo "- " . $u['nombre'] . " | " . $u['email'] . " | " . $u['rol'] . "\n";
}
