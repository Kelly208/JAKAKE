<?php
$conn = new PDO('mysql:host=localhost;dbname=papeleria_jakake;charset=utf8mb4', 'root', '2409');
$conn->exec("SET NAMES utf8mb4");

echo "Corrigiendo motivos...\n";

$sql = "UPDATE devoluciones 
        SET motivo = 'Cliente compró de más'
        WHERE motivo LIKE '%compr?? de m??s%'";

$result = $conn->exec($sql);
echo "Registros actualizados: $result\n";

echo "\nVerificando otros casos con ??:\n";
$stmt = $conn->query("SELECT id, motivo FROM devoluciones WHERE motivo LIKE '%??%'");
$rows = $stmt->fetchAll(PDO::FETCH_ASSOC);

if (empty($rows)) {
    echo "✓ No hay más registros con ??\n";
} else {
    foreach ($rows as $row) {
        echo "ID {$row['id']}: {$row['motivo']}\n";
    }
}
