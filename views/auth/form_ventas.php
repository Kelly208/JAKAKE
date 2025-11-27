<?php
$title = 'Registrar Venta';
ob_start();
?>
<form method="POST" action="/ventas/procesar">
    <input type="hidden" name="csrf_token" value="<?= \App\Utils\Security::generateToken() ?>">
    <div class="mb-3">
        <label>Cliente ID</label>
        <input type="number" name="cliente_id" class="form-control" required>
    </div>
    <div class="mb-3">
        <label>Producto ID</label>
        <input type="number" name="producto_id" class="form-control" required>
    </div>
    <div class="mb-3">
        <label>Cantidad</label>
        <input type="number" name="cantidad" class="form-control" required>
    </div>
    <div class="mb-3">
        <label>Total</label>
        <input type="number" step="0.01" name="total" class="form-control" required>
    </div>
    <button type="submit" class="btn btn-success">Registrar</button>
</form>
<?php $content = ob_get_clean(); ?>
<?php require __DIR__ . '/layout.php'; ?>
