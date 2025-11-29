<?php
// Simulate exactly what the web application does
session_start();
$_SESSION['user_id'] = 1;

// Use direct PDO connection like Database class does
$db = new PDO('mysql:host=localhost;dbname=papeleria_jakake;charset=utf8mb4', 'root', '2409', [
    PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
    PDO::ATTR_EMULATE_PREPARES   => false,
]);

// Simulate POST data
$_POST = [
    'cliente_id' => '1',
    'medio_pago' => 'efectivo',
    'productos' => '[{"producto_id":1,"cantidad":1,"precio":4500}]'
];

echo "=== Simulating Web Request ===\n\n";

try {
    $cliente_id = intval($_POST['cliente_id']);
    $medio_pago = $_POST['medio_pago'];
    $productos_json = $_POST['productos'];
    
    echo "Cliente ID: $cliente_id\n";
    echo "Medio Pago: $medio_pago\n";
    echo "Productos JSON: $productos_json\n\n";
    
    $productos = json_decode($productos_json, true);
    if (empty($productos)) {
        throw new Exception("Debe agregar al menos un producto");
    }
    
    // Preparar el JSON en el formato que espera el procedimiento
    $productos_array = [];
    foreach ($productos as $prod) {
        $productos_array[] = [
            'producto_id' => intval($prod['producto_id']),
            'cantidad' => intval($prod['cantidad']),
            'precio_unitario' => floatval($prod['precio'])
        ];
    }
    
    $productos_json_final = json_encode($productos_array);
    $usuario_id = $_SESSION['user_id'];
    
    echo "Productos JSON Final: $productos_json_final\n";
    echo "Usuario ID: $usuario_id\n\n";
    
    echo "Calling stored procedure...\n";
    $stmt = $db->prepare("CALL sp_registrar_venta_completa(?, ?, ?, ?, @venta_id, @total_final)");
    $stmt->execute([
        $cliente_id,
        $usuario_id,
        $medio_pago,
        $productos_json_final
    ]);
    
    // Obtener los valores de salida del procedimiento
    $result = $db->query("SELECT @venta_id as venta_id, @total_final as total_final")->fetch(PDO::FETCH_ASSOC);
    $venta_id = $result['venta_id'] ?? null;
    
    echo "\n✓ SUCCESS!\n";
    echo "Venta ID: $venta_id\n";
    echo "Total: \${$result['total_final']}\n";
    
} catch (Exception $e) {
    echo "\n✗ ERROR: " . $e->getMessage() . "\n";
    echo "Code: " . $e->getCode() . "\n";
    if ($e instanceof PDOException) {
        echo "SQL State: " . $e->errorInfo[0] . "\n";
        print_r($e->errorInfo);
    }
}
