<?php
$conn = new PDO('mysql:host=localhost;dbname=papeleria_jakake;charset=utf8mb4', 'root', '2409');

$sql = file_get_contents('database/add_medio_pago.sql');

try {
    $conn->exec($sql);
    echo "✓ Columna medio_pago agregada y datos actualizados\n\n";
    
    echo "=== VERIFICANDO ===\n";
    $stmt = $conn->query("SELECT id, medio_pago, total FROM ventas LIMIT 5");
    while($row = $stmt->fetch()) {
        echo "Venta #" . $row['id'] . ": " . $row['medio_pago'] . " - $" . $row['total'] . "\n";
    }
} catch (PDOException $e) {
    echo "✗ Error: " . $e->getMessage() . "\n";
}
