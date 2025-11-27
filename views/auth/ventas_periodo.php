<?php
$title = 'Ventas por Período';
ob_start();
?>
<form method="GET" action="/ventas/periodo">
    <label>Desde:</label>
    <input type="date" name="desde" required>
    <label>Hasta:</label>
    <input type="date" name="hasta" required>
    <button type="submit" class="btn btn-primary">Consultar</button>
</form>
<?php if (!empty($ventas)): ?>
<table class="table mt-3">
    <thead><tr><th>ID</th><th>Cliente</th><th>Total</th><th>Fecha</th></tr></thead>
    <tbody>
        <?php foreach ($ventas as $v): ?>
        <tr>
            <td><?= $v['id'] ?></td>
            <td><?= htmlspecialchars($v['cliente']) ?></td>
            <td>$<?= number_format($v['total'], 2) ?></td>
            <td><?= $v['fecha'] ?></td>
        </tr>
        <?php endforeach; ?>
    </tbody>
</table>
<?php endif; ?>
<?php $content = ob_get_clean(); ?>
<?php require __DIR__ . '/layout.php'; ?>

