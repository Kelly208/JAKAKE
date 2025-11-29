<?php
use App\Utils\Session;
require_once __DIR__ . '/../layouts/header.php';
require_once __DIR__ . '/../layouts/sidebar.php';
?>

<nav aria-label="breadcrumb">
    <ol class="breadcrumb">
        <li class="breadcrumb-item"><a href="/dashboard">Dashboard</a></li>
        <li class="breadcrumb-item active">Proveedores</li>
    </ol>
</nav>

<div class="d-flex justify-content-between align-items-center mb-4">
    <h1 class="h2">
        <i class="bi bi-truck"></i> Proveedores
    </h1>
    <a href="/proveedores/crear" class="btn btn-primary">
        <i class="bi bi-plus-circle"></i> Nuevo Proveedor
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
                    <i class="bi bi-list-ul"></i> Listado de Proveedores
                </h6>
            </div>
            <div class="col-auto">
                <input type="text" id="searchInput" class="form-control" placeholder="Buscar proveedores...">
            </div>
        </div>
    </div>
    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-hover" id="proveedoresTable">
                <thead class="table-light">
                    <tr>
                        <th>NIT</th>
                        <th>Nombre</th>
                        <th>Contacto</th>
                        <th>Dirección</th>
                        <th>Productos</th>
                        <th>Estado</th>
                        <th>Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (empty($proveedores)): ?>
                    <tr>
                        <td colspan="7" class="text-center">
                            <div class="alert alert-info mb-0">
                                <i class="bi bi-info-circle"></i> No hay proveedores registrados
                            </div>
                        </td>
                    </tr>
                    <?php else: ?>
                        <?php foreach ($proveedores as $proveedor): ?>
                        <tr>
                            <td><code><?= htmlspecialchars($proveedor['nit']) ?></code></td>
                            <td>
                                <strong><?= htmlspecialchars($proveedor['nombre']) ?></strong>
                                <?php if ($proveedor['contacto']): ?>
                                <br><small class="text-muted"><i class="bi bi-person"></i> <?= htmlspecialchars($proveedor['contacto']) ?></small>
                                <?php endif; ?>
                            </td>
                            <td>
                                <?php if ($proveedor['telefono']): ?>
                                <small><i class="bi bi-telephone"></i> <?= htmlspecialchars($proveedor['telefono']) ?></small><br>
                                <?php endif; ?>
                                <?php if ($proveedor['email']): ?>
                                <small><i class="bi bi-envelope"></i> <?= htmlspecialchars($proveedor['email']) ?></small>
                                <?php endif; ?>
                            </td>
                            <td>
                                <?= htmlspecialchars($proveedor['direccion']) ?>
                            </td>
                            <td>
                                <span class="badge bg-info">
                                    <?= $proveedor['total_productos'] ?> producto(s)
                                </span>
                            </td>
                            <td>
                                <span class="badge bg-<?= $proveedor['estado'] === 'activo' ? 'success' : 'secondary' ?>">
                                    <?= ucfirst($proveedor['estado']) ?>
                                </span>
                            </td>
                            <td>
                                <a href="/proveedores/editar/<?= $proveedor['id'] ?>" class="btn btn-sm btn-primary" title="Editar">
                                    <i class="bi bi-pencil"></i>
                                </a>
                                <form method="POST" action="/proveedores/eliminar/<?= $proveedor['id'] ?>" style="display: inline;">
                                    <button type="submit" class="btn btn-sm btn-danger" 
                                            data-confirm="¿Estás seguro de eliminar este proveedor?"
                                            title="Eliminar">
                                        <i class="bi bi-trash"></i>
                                    </button>
                                </form>
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
// Búsqueda en tiempo real
jakake.searchTable('searchInput', 'proveedoresTable');
</script>

<?php require_once __DIR__ . '/../layouts/footer.php'; ?>
