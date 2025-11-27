<?php
$title = 'Crear Producto';
ob_start();
?>
<form method="POST" action="/productos/crear">
    <input type="hidden" name="csrf_token" value="<?= \App\Utils\Security::generateToken() ?>">
    <div class="mb-3">
        <label>Nombre</label>
        <input type="text" name="nombre" class="form-control" required>
    </div>
    <div class="mb-3">
        <label>Precio</label>
        <input type="number" step="0.01" name="precio" class="form-control" required>
    </div>
    <div class="mb-3">
        <label>Stock</label>
        <input type="number" name="stock" class="form-control" required>
    </div>
    <div class="mb-3">
        <label>Proveedor ID</label>
        <input type="number" name="proveedor_id" class="form-control" required>
    </div>
    <button type="submit" class="btn btn-success">Guardar</button>
</form>
<?php $content = ob_get_clean(); ?>
<?php require __DIR__ . '/layout.php'; ?>
