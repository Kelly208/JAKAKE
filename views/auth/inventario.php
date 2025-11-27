<?php
$title = 'Inventario Actual';
ob_start();
?>
<h2>📦 Inventario Actual</h2>
<table class="table table-striped">
    <thead><tr><th>ID</th><th>Nombre</th><th>Stock</th><th>Precio</th></tr></thead>
    <tbody>
        <?php foreach ($productos as $p): ?>
        <tr>
            <td><?= $p['id'] ?></td>
            <td><?= htmlspecialchars($p['nombre']) ?></td>
            <td><?= $p['stock'] ?></td>
            <td>$<?= number_format($p['precio'], 2) ?></td>
        </tr>
        <?php endforeach; ?>
    </tbody>
</table>
<?php $content = ob_get_clean(); ?>
<?php require __DIR__ . '/layout.php'; ?>
