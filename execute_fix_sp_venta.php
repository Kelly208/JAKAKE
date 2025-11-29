<?php
$conn = new PDO('mysql:host=localhost;dbname=papeleria_jakake;charset=utf8mb4', 'root', '2409');
$conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

try {
    // Drop procedure
    $conn->exec("DROP PROCEDURE IF EXISTS sp_procesar_devolucion");
    
    // Create procedure
    $sql = file_get_contents(__DIR__ . '/database/sp_procesar_devolucion.sql');
    
    // Remove the DROP statement from the file content if it exists
    $sql = preg_replace('/DROP PROCEDURE IF EXISTS sp_procesar_devolucion;?\s*/i', '', $sql);
    $sql = preg_replace('/CREATE PROCEDURE/i', 'CREATE PROCEDURE', $sql);
    
    $conn->exec($sql);
    echo "✓ Procedimiento sp_procesar_devolucion actualizado con cliente_id\n";
} catch (PDOException $e) {
    echo "✗ Error: " . $e->getMessage() . "\n";
}
