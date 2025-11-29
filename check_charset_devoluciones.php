<?php
$conn = new PDO('mysql:host=localhost;dbname=papeleria_jakake;charset=utf8mb4', 'root', '2409');
$conn->exec("SET NAMES utf8mb4");

echo "=== MOTIVOS EN DEVOLUCIONES ===\n";
$stmt = $conn->query("SELECT motivo, HEX(motivo) as hex_motivo FROM devoluciones ORDER BY fecha_devolucion DESC LIMIT 10");
$motivos = $stmt->fetchAll(PDO::FETCH_ASSOC);

foreach ($motivos as $m) {
    echo "Motivo: " . $m['motivo'] . "\n";
    echo "HEX: " . $m['hex_motivo'] . "\n\n";
}

echo "\n=== VERIFICAR CHARSET DE TABLA ===\n";
$stmt = $conn->query("SHOW CREATE TABLE devoluciones");
$result = $stmt->fetch();
echo $result['Create Table'] . "\n";
