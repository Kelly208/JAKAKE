<?php
$conn = new PDO('mysql:host=localhost;dbname=papeleria_jakake;charset=utf8mb4', 'root', '2409');
$conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

echo "=== Testing sp_registrar_venta_completa ===\n\n";

// Test data
$cliente_id = 1;
$usuario_id = 1;
$medio_pago = 'efectivo';
$productos = json_encode([
    [
        'producto_id' => 1,
        'cantidad' => 2,
        'precio_unitario' => 5000
    ]
]);

echo "Cliente ID: $cliente_id\n";
echo "Usuario ID: $usuario_id\n";
echo "Medio pago: $medio_pago\n";
echo "Productos: $productos\n\n";

try {
    // Check if cliente exists
    $stmt = $conn->prepare("SELECT id, nombre FROM clientes WHERE id = ?");
    $stmt->execute([$cliente_id]);
    $cliente = $stmt->fetch();
    echo "Cliente found: " . ($cliente ? $cliente['nombre'] : "NO") . "\n";
    
    // Check if usuario exists
    $stmt = $conn->prepare("SELECT id, nombre FROM usuarios WHERE id = ?");
    $stmt->execute([$usuario_id]);
    $usuario = $stmt->fetch();
    echo "Usuario found: " . ($usuario ? $usuario['nombre'] : "NO") . "\n";
    
    // Check if producto exists and has stock
    $stmt = $conn->prepare("SELECT id, nombre, cantidad, precio FROM productos WHERE id = 1");
    $stmt->execute();
    $producto = $stmt->fetch();
    echo "Producto found: " . ($producto ? $producto['nombre'] . " (Stock: " . $producto['cantidad'] . ", Precio: $" . $producto['precio'] . ")" : "NO") . "\n\n";
    
    if (!$cliente || !$usuario || !$producto) {
        echo "ERROR: Missing required data\n";
        exit;
    }
    
    // Test the procedure
    echo "Calling procedure...\n";
    $stmt = $conn->prepare("CALL sp_registrar_venta_completa(?, ?, ?, ?, @venta_id, @total_final)");
    $stmt->execute([$cliente_id, $usuario_id, $medio_pago, $productos]);
    
    // Get output parameters
    $result = $conn->query("SELECT @venta_id as venta_id, @total_final as total_final")->fetch(PDO::FETCH_ASSOC);
    
    echo "\n✓ SUCCESS!\n";
    echo "Venta ID: " . $result['venta_id'] . "\n";
    echo "Total: $" . number_format($result['total_final'], 2) . "\n";
    
} catch (PDOException $e) {
    echo "\n✗ ERROR: " . $e->getMessage() . "\n";
    echo "Error Code: " . $e->getCode() . "\n";
}
