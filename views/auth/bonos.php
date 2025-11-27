<?php
$title = 'Bonos Activos';
ob_start();
?>
<h2>🎁 Bonos Activos</h2>
<table class="table">
    <thead><tr><th>Nombre</th><th>Descuento</th><th>Vigencia</th></tr></thead>
    <tbody>
        <?php foreach ($bonos as $b): ?>
        <tr>
            <td><?= htmlspecialchars($b['nombre']) ?></td>
            <td><?= $b['descuento'] ?>%</td>
            <td><?= $b['vigencia'] ?></td>
        </tr>
        <?php endforeach; ?>
    </tbody>
</table>
<?php $content = ob_get_clean(); ?>
<?php require __DIR__ . '/layout.php'; ?>
