<?php
$title = 'Productos Más Vendidos';
ob_start();
?>
<h2>🔥 Top Productos</h2>
<ul>
    <?php foreach ($topProductos as $p): ?>
    <li><?= htmlspecialchars($p['nombre']) ?> - <?= $p['cantidad'] ?> ventas</li>
    <?php endforeach; ?>
</ul>
<?php $content = ob_get_clean(); ?>
<?php require __DIR__ . '/layout.php'; ?>
