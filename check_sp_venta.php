<?php
$conn = new PDO('mysql:host=localhost;dbname=papeleria_jakake;charset=utf8mb4', 'root', '2409');

echo "=== sp_registrar_venta_completa ===\n";
$stmt = $conn->query("SHOW CREATE PROCEDURE sp_registrar_venta_completa");
$row = $stmt->fetch();
echo $row['Create Procedure'] . "\n";
