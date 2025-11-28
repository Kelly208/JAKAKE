<?php
use App\Utils\Session;
require_once __DIR__ . '/../layouts/header.php';
require_once __DIR__ . '/../layouts/sidebar.php';
?>

<nav aria-label="breadcrumb">
    <ol class="breadcrumb">
        <li class="breadcrumb-item"><a href="/dashboard">Dashboard</a></li>
        <li class="breadcrumb-item active">Productos</li>
    </ol>
</nav>

<div class="d-flex justify-content-between align-items-center mb-4">
    <h1 class="h2">
        <i class="bi bi-box-seam"></i> Productos
    </h1>
    <a href="/productos/crear" class="btn btn-primary">
        <i class="bi bi-plus-circle"></i> Nuevo Producto
    </a>
</div>

<?php if (Session::get('success')): ?>
<div class="alert alert-success alert-dismissible fade show">
    <?= Session::get('success') ?>
    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
</div>
<?php Session::delete('success'); endif; ?>

<?php if (Session::get('error')): ?>
<div class="alert alert-danger alert-dismissible fade show">
    <?= Session::get('error') ?>
    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
</div>
<?php Session::delete('error'); endif; ?>

<div class="card shadow">
    <div class="card-header">
        <div class="row align-items-center">
            <div class="col">
                <h6 class="m-0 font-weight-bold text-primary">
                    <i class="bi bi-list-ul"></i> Listado de Productos
                </h6>
            </div>
            <div class="col-auto">
                <input type="text" id="searchInput" class="form-control" placeholder="Buscar productos...">
            </div>
        </div>
    </div>
    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-hover" id="productosTable">
                <thead class="table-light">
                    <tr>
                        <th>Código</th>
                        <th>Nombre</th>
                        <th>Tipo</th>
                        <th>Precio</th>
                        <th>Stock</th>
                        <th>Mínimo</th>
                        <th>Proveedor</th>
                        <th>Estado</th>
                        <th>Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (empty($productos)): ?>
                    <tr>
                        <td colspan="9" class="text-center">
                            <div class="alert alert-info mb-0">
                                <i class="bi bi-info-circle"></i> No hay productos registrados
                            </div>
                        </td>
                    </tr>
                    <?php else: ?>
                        <?php foreach ($productos as $producto): ?>
                        <tr>
                            <td><code><?= htmlspecialchars($producto['codigo']) ?></code></td>
                            <td>
                                <strong><?= htmlspecialchars($producto['nombre']) ?></strong>
                                <?php if ($producto['descripcion']): ?>
                                <br><small class="text-muted"><?= htmlspecialchars(substr($producto['descripcion'], 0, 50)) ?>...</small>
                                <?php endif; ?>
                            </td>
                            <td>
                                <span class="badge bg-secondary">
                                    <?= ucfirst($producto['tipo']) ?>
                                </span>
                            </td>
                            <td class="currency">$<?= number_format($producto['precio'], 0, ',', '.') ?></td>
                            <td>
                                <?php if ($producto['cantidad'] < $producto['cantidad_minima']): ?>
                                    <span class="badge bg-danger"><?= $producto['cantidad'] ?></span>
                                <?php elseif ($producto['cantidad'] < $producto['cantidad_minima'] * 2): ?>
                                    <span class="badge bg-warning text-dark"><?= $producto['cantidad'] ?></span>
                                <?php else: ?>
                                    <span class="badge bg-success"><?= $producto['cantidad'] ?></span>
                                <?php endif; ?>
                            </td>
                            <td><?= $producto['cantidad_minima'] ?></td>
                            <td><?= htmlspecialchars($producto['proveedor_nombre'] ?? 'Sin proveedor') ?></td>
                            <td>
                                <span class="badge bg-<?= $producto['estado'] === 'activo' ? 'success' : 'secondary' ?>">
                                    <?= ucfirst($producto['estado']) ?>
                                </span>
                            </td>
                            <td>
                                <a href="/productos/editar/<?= $producto['id'] ?>" class="btn btn-sm btn-primary" title="Editar">
                                    <i class="bi bi-pencil"></i>
                                </a>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    jakake.searchTable('searchInput', 'productosTable');
});
</script>

<?php require_once __DIR__ . '/../layouts/footer.php'; ?>
