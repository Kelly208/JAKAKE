<?php
$conn = new PDO('mysql:host=localhost;dbname=papeleria_jakake;charset=utf8mb4', 'root', '2409');

echo "=== VENTAS TABLE STRUCTURE ===\n";
$stmt = $conn->query("DESCRIBE ventas");
while ($row = $stmt->fetch()) {
    echo $row['Field'] . " - " . $row['Type'] . "\n";
}
