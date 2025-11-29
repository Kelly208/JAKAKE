<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

$conn = new PDO('mysql:host=localhost;dbname=papeleria_jakake;charset=utf8mb4', 'root', '2409');
$conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

echo "=== Testing Venta with Real Data ===\n\n";

// Get a real cliente
$stmt = $conn->query("SELECT id, nombre FROM clientes WHERE acepto_politicas = 1 LIMIT 1");
$cliente = $stmt->fetch();
echo "Cliente: {$cliente['id']} - {$cliente['nombre']}\n";

// Get a real usuario
$stmt = $conn->query("SELECT id, nombre FROM usuarios LIMIT 1");
$usuario = $stmt->fetch();
echo "Usuario: {$usuario['id']} - {$usuario['nombre']}\n";

// Get a real producto
$stmt = $conn->query("SELECT id, nombre, precio, cantidad FROM productos WHERE estado = 'activo' AND cantidad > 5 LIMIT 1");
$producto = $stmt->fetch();
echo "Producto: {$producto['id']} - {$producto['nombre']} (Precio: {$producto['precio']}, Stock: {$producto['cantidad']})\n\n";

$cliente_id = $cliente['id'];
$usuario_id = $usuario['id'];
$medio_pago = 'efectivo';

// Test with the exact format the controller uses
$productos = json_encode([
    [
        'producto_id' => (int)$producto['id'],
        'cantidad' => 2,
        'precio_unitario' => (float)$producto['precio']
    ]
]);

echo "Parameters:\n";
echo "- cliente_id: $cliente_id\n";
echo "- usuario_id: $usuario_id\n";
echo "- medio_pago: $medio_pago\n";
echo "- productos: $productos\n\n";

try {
    echo "Calling sp_registrar_venta_completa...\n";
    
    $stmt = $conn->prepare("CALL sp_registrar_venta_completa(?, ?, ?, ?, @venta_id, @total_final)");
    $stmt->execute([$cliente_id, $usuario_id, $medio_pago, $productos]);
    
    $result = $conn->query("SELECT @venta_id as venta_id, @total_final as total_final")->fetch(PDO::FETCH_ASSOC);
    
    echo "\n✓ SUCCESS!\n";
    echo "Venta ID: {$result['venta_id']}\n";
    echo "Total: \${$result['total_final']}\n";
    
    // Verify the venta was created
    $stmt = $conn->prepare("SELECT * FROM ventas WHERE id = ?");
    $stmt->execute([$result['venta_id']]);
    $venta = $stmt->fetch(PDO::FETCH_ASSOC);
    
    echo "\nVenta details:\n";
    print_r($venta);
    
    // Check detalle_venta
    $stmt = $conn->prepare("SELECT * FROM detalle_venta WHERE venta_id = ?");
    $stmt->execute([$result['venta_id']]);
    $detalles = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    echo "\nDetalle venta:\n";
    print_r($detalles);
    
} catch (PDOException $e) {
    echo "\n✗ ERROR: {$e->getMessage()}\n";
    echo "Error Code: {$e->getCode()}\n";
    echo "SQL State: {$e->errorInfo[0]}\n";
    
    // Try to get more details
    if (strpos($e->getMessage(), 'Stock insuficiente') !== false) {
        echo "\nStock issue detected. Checking producto stock...\n";
        $stmt = $conn->prepare("SELECT id, nombre, cantidad FROM productos WHERE id = ?");
        $stmt->execute([$producto['id']]);
        $p = $stmt->fetch();
        echo "Current stock for producto {$p['id']}: {$p['cantidad']}\n";
    }
}
