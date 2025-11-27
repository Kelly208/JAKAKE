<?php
$title = 'Auditoría Reciente';
ob_start();
?>
<h2>📝 Auditoría</h2>
<table class="table">
    <thead><tr><th>Acción</th><th>Usuario</th><th>Fecha</th></tr></thead>
    <tbody>
        <?php foreach ($auditoria as $a): ?>
        <tr>
            <td><?= htmlspecialchars($a['accion']) ?></td>
            <td><?= htmlspecialchars($a['usuario']) ?></td>
            <td><?= $a['fecha'] ?></td>
        </tr>
        <?php endforeach; ?>
    </tbody>
</table>
<?php $content = ob_get_clean(); ?>
<?php require __DIR__ . '/layout.php'; ?>
