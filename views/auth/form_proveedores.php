<?php
$title = 'Crear Proveedor';
ob_start();
?>
<form method="POST" action="/proveedores/crear">
    <input type="hidden" name="csrf_token" value="<?= \App\Utils\Security::generateToken() ?>">
    <div class="mb-3">
        <label>Nombre</label>
        <input type="text" name="nombre" class="form-control" required>
    </div>
    <div class="mb-3">
        <label>Contacto</label>
        <input type="text" name="contacto" class="form-control" required>
    </div>
    <div class="mb-3">
        <label>Teléfono</label>
        <input type="text" name="telefono" class="form-control" required>
    </div>
    <button type="submit" class="btn btn-success">Guardar</button>
</form>
<?php $content = ob_get_clean(); ?>
<?php require __DIR__ . '/layout.php'; ?>
