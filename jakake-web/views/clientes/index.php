<?php
use App\Utils\Session;
require_once __DIR__ . '/../layouts/header.php';
require_once __DIR__ . '/../layouts/sidebar.php';
?>

<nav aria-label="breadcrumb">
    <ol class="breadcrumb">
        <li class="breadcrumb-item"><a href="/dashboard">Dashboard</a></li>
        <li class="breadcrumb-item active">Clientes</li>
    </ol>
</nav>

<div class="d-flex justify-content-between align-items-center mb-4">
    <h1 class="h2">
        <i class="bi bi-people"></i> Clientes
    </h1>
    <a href="/clientes/crear" class="btn btn-primary">
        <i class="bi bi-plus-circle"></i> Nuevo Cliente
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
                    <i class="bi bi-list-ul"></i> Listado de Clientes
                </h6>
            </div>
            <div class="col-auto">
                <input type="text" id="searchInput" class="form-control" placeholder="Buscar clientes...">
            </div>
        </div>
    </div>
    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-hover" id="clientesTable">
                <thead class="table-light">
                    <tr>
                        <th>Cédula</th>
                        <th>Nombre Completo</th>
                        <th>Contacto</th>
                        <th>Compras</th>
                        <th>Políticas</th>
                        <th>Estado</th>
                        <th>Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (empty($clientes)): ?>
                    <tr>
                        <td colspan="7" class="text-center">
                            <div class="alert alert-info mb-0">
                                <i class="bi bi-info-circle"></i> No hay clientes registrados
                            </div>
                        </td>
                    </tr>
                    <?php else: ?>
                        <?php foreach ($clientes as $cliente): ?>
                        <tr>
                            <td><code><?= htmlspecialchars($cliente['cedula']) ?></code></td>
                            <td>
                                <strong><?= htmlspecialchars($cliente['nombre']) ?></strong>
                                <?php if ($cliente['direccion']): ?>
                                <br><small class="text-muted"><i class="bi bi-geo-alt"></i> <?= htmlspecialchars($cliente['direccion']) ?></small>
                                <?php endif; ?>
                            </td>
                            <td>
                                <?php if ($cliente['telefono']): ?>
                                <small><i class="bi bi-telephone"></i> <?= htmlspecialchars($cliente['telefono']) ?></small><br>
                                <?php endif; ?>
                                <?php if ($cliente['email']): ?>
                                <small><i class="bi bi-envelope"></i> <?= htmlspecialchars($cliente['email']) ?></small>
                                <?php endif; ?>
                            </td>
                            <td>
                                <span class="badge bg-info">
                                    <?= $cliente['total_compras'] ?> compra(s)
                                </span>
                            </td>
                            <td>
                                <?php if ($cliente['fecha_aceptacion']): ?>
                                    <span class="badge bg-success" title="Aceptó el <?= date('d/m/Y', strtotime($cliente['fecha_aceptacion'])) ?>">
                                        <i class="bi bi-check-circle"></i> Aceptadas
                                    </span>
                                <?php else: ?>
                                    <span class="badge bg-warning text-dark">
                                        <i class="bi bi-exclamation-triangle"></i> Pendiente
                                    </span>
                                <?php endif; ?>
                            </td>
                            <td>
                                <span class="badge bg-<?= $cliente['acepto_politicas'] ? 'success' : 'secondary' ?>">
                                    <?= $cliente['acepto_politicas'] ? 'Activo' : 'Inactivo' ?>
                                </span>
                            </td>
                            <td>
                                <a href="/clientes/editar/<?= $cliente['id'] ?>" class="btn btn-sm btn-primary" title="Editar">
                                    <i class="bi bi-pencil"></i>
                                </a>
                                <a href="/clientes/historial/<?= $cliente['id'] ?>" class="btn btn-sm btn-info" title="Ver historial">
                                    <i class="bi bi-clock-history"></i>
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
    jakake.searchTable('searchInput', 'clientesTable');
});
</script>

<?php require_once __DIR__ . '/../layouts/footer.php'; ?>
