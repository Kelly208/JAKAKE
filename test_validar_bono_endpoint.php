<?php
// Test directo del endpoint
$codigo = 'BONO-20251128-000001';
$cliente_id = 1;

$url = "http://localhost:8000/ventas/validar-bono?codigo=" . urlencode($codigo) . "&cliente_id=" . $cliente_id;

echo "Testing URL: $url\n\n";

$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, $url);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_HEADER, true);

$response = curl_exec($ch);
$httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
curl_close($ch);

echo "HTTP Code: $httpCode\n\n";
echo "Response:\n";
echo $response;
