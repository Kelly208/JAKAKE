<?php
$conn = new PDO('mysql:host=localhost;dbname=papeleria_jakake;charset=utf8mb4', 'root', '2409');

echo "=== PRODUCTOS TABLE STRUCTURE ===\n";
$stmt = $conn->query("DESCRIBE productos");
while ($row = $stmt->fetch()) {
    echo $row['Field'] . " - " . $row['Type'] . "\n";
}

echo "\n=== SAMPLE PRODUCTO ===\n";
$stmt = $conn->query("SELECT * FROM productos LIMIT 1");
$producto = $stmt->fetch(PDO::FETCH_ASSOC);
if ($producto) {
    foreach ($producto as $key => $value) {
        echo "$key: $value\n";
    }
} else {
    echo "No productos found\n";
}
