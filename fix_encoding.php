<?php
$conn = new PDO('mysql:host=localhost;dbname=papeleria_jakake;charset=utf8mb4', 'root', '2409');

$sql = file_get_contents('database/fix_tildes.sql');

try {
    $conn->exec($sql);
    echo "✓ Datos actualizados con tildes correctos\n\n";
    
    echo "=== VERIFICANDO ===\n";
    $stmt = $conn->query("SELECT nombre FROM usuarios WHERE id <= 4");
    while($row = $stmt->fetch()) {
        echo $row['nombre'] . "\n";
    }
} catch (PDOException $e) {
    echo "✗ Error: " . $e->getMessage() . "\n";
}
