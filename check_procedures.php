<?php
$conn = new PDO('mysql:host=localhost;dbname=papeleria_jakake;charset=utf8mb4', 'root', '2409');

echo "=== sp_productos_mas_vendidos ===\n";
$stmt = $conn->query("SHOW CREATE PROCEDURE sp_productos_mas_vendidos");
$row = $stmt->fetch();
echo $row['Create Procedure'] . "\n\n";

echo "=== sp_productos_bajo_stock ===\n";
$stmt = $conn->query("SHOW CREATE PROCEDURE sp_productos_bajo_stock");
$row = $stmt->fetch();
echo $row['Create Procedure'] . "\n";
