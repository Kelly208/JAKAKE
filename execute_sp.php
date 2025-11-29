<?php
$conn = new PDO('mysql:host=localhost;dbname=papeleria_jakake;charset=utf8mb4', 'root', '2409');

$sql = file_get_contents('database/sp_procesar_devolucion.sql');

// Ejecutar el SQL
try {
    $conn->exec($sql);
    echo "✓ Procedimiento sp_procesar_devolucion creado exitosamente\n";
} catch (PDOException $e) {
    echo "✗ Error: " . $e->getMessage() . "\n";
}

// Verificar encoding de caracteres en la base de datos
echo "\n=== VERIFICANDO CARACTERES ===\n";
$stmt = $conn->query("SELECT nombre FROM usuarios LIMIT 3");
while($row = $stmt->fetch()) {
    echo $row['nombre'] . "\n";
}
