<?php
use App\Utils\Session;
require_once __DIR__ . '/../layouts/header.php';
require_once __DIR__ . '/../layouts/sidebar.php';
?>

<nav aria-label="breadcrumb">
    <ol class="breadcrumb">
        <li class="breadcrumb-item"><a href="/dashboard">Dashboard</a></li>
        <li class="breadcrumb-item active">Devoluciones</li>
    </ol>
</nav>

<div class="d-flex justify-content-between align-items-center mb-4">
    <h1 class="h2">
        <i class="bi bi-arrow-counterclockwise"></i> Devoluciones
    </h1>
    <a href="/devoluciones/nueva" class="btn btn-primary btn-lg">
        <i class="bi bi-plus-circle"></i> Nueva Devolución
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
                    <i class="bi bi-list-ul"></i> Historial de Devoluciones
                </h6>
            </div>
            <div class="col-auto">
                <input type="text" id="searchInput" class="form-control" placeholder="Buscar devoluciones...">
            </div>
        </div>
    </div>
    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-hover" id="devolucionesTable">
                <thead class="table-light">
                    <tr>
                        <th>ID</th>
                        <th>Fecha</th>
                        <th>Venta Original</th>
                        <th>Cliente</th>
                        <th>Usuario</th>
                        <th>Productos</th>
                        <th>Monto</th>
                        <th>Motivo</th>
                        <th>Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (empty($devoluciones)): ?>
                    <tr>
                        <td colspan="9" class="text-center">
                            <div class="alert alert-info mb-0">
                                <i class="bi bi-info-circle"></i> No hay devoluciones registradas
                            </div>
                        </td>
                    </tr>
                    <?php else: ?>
                        <?php foreach ($devoluciones as $devolucion): ?>
                        <tr>
                            <td><code>#<?= $devolucion['id'] ?></code></td>
                            <td><?= date('d/m/Y H:i', strtotime($devolucion['fecha_devolucion'])) ?></td>
                            <td>
                                <a href="/ventas/detalle/<?= $devolucion['venta_id'] ?>" class="text-primary">
                                    #<?= $devolucion['venta_id'] ?>
                                </a>
                                <br><small class="text-muted"><?= date('d/m/Y', strtotime($devolucion['fecha_venta'])) ?></small>
                            </td>
                            <td>
                                <?php if ($devolucion['cliente_nombre']): ?>
                                    <?= htmlspecialchars($devolucion['cliente_nombre']) ?>
                                <?php else: ?>
                                    <span class="text-muted">Cliente general</span>
                                <?php endif; ?>
                            </td>
                            <td><?= htmlspecialchars($devolucion['usuario_nombre']) ?></td>
                            <td>
                                <span class="badge bg-secondary">
                                    <?= $devolucion['total_productos'] ?> producto(s)
                                </span>
                            </td>
                            <td class="currency">
                                <strong>$<?= number_format($devolucion['valor_total'], 0, ',', '.') ?></strong>
                            </td>
                            <td>
                                <small><?= htmlspecialchars(substr($devolucion['motivo'], 0, 30)) ?>...</small>
                            </td>
                            <td>
                                <a href="/devoluciones/detalle/<?= $devolucion['id'] ?>" class="btn btn-sm btn-info" title="Ver detalle">
                                    <i class="bi bi-eye"></i>
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
    jakake.searchTable('searchInput', 'devolucionesTable');
});
</script>

<?php require_once __DIR__ . '/../layouts/footer.php'; ?>
