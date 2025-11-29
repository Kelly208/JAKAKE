<?php
$conn = new PDO('mysql:host=localhost;dbname=papeleria_jakake;charset=utf8mb4', 'root', '2409');

echo "=== TABLA USUARIOS ===\n";
$stmt = $conn->query('DESCRIBE usuarios');
while($row = $stmt->fetch()) {
    echo $row['Field'] . "\n";
}

echo "\n=== TABLA ACEPTACION_POLITICAS ===\n";
$stmt = $conn->query('DESCRIBE aceptacion_politicas');
while($row = $stmt->fetch()) {
    echo $row['Field'] . "\n";
}

echo "\n=== STORED PROCEDURES ===\n";
$stmt = $conn->query("SHOW PROCEDURE STATUS WHERE Db = 'papeleria_jakake'");
while($row = $stmt->fetch()) {
    echo $row['Name'] . "\n";
}
