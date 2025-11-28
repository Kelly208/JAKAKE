<?php
use App\Utils\Session;
require_once __DIR__ . '/../layouts/header.php';
require_once __DIR__ . '/../layouts/sidebar.php';
?>

<nav aria-label="breadcrumb">
    <ol class="breadcrumb">
        <li class="breadcrumb-item"><a href="/dashboard">Dashboard</a></li>
        <li class="breadcrumb-item active">Ventas</li>
    </ol>
</nav>

<div class="d-flex justify-content-between align-items-center mb-4">
    <h1 class="h2">
        <i class="bi bi-cart-check"></i> Ventas
    </h1>
    <a href="/ventas/nueva" class="btn btn-primary btn-lg">
        <i class="bi bi-plus-circle"></i> Nueva Venta
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
                    <i class="bi bi-list-ul"></i> Historial de Ventas
                </h6>
            </div>
            <div class="col-auto">
                <input type="text" id="searchInput" class="form-control" placeholder="Buscar ventas...">
            </div>
        </div>
    </div>
    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-hover" id="ventasTable">
                <thead class="table-light">
                    <tr>
                        <th>ID</th>
                        <th>Fecha</th>
                        <th>Cliente</th>
                        <th>Cajero</th>
                        <th>Productos</th>
                        <th>Total</th>
                        <th>Medio de Pago</th>
                        <th>Estado</th>
                        <th>Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (empty($ventas)): ?>
                    <tr>
                        <td colspan="9" class="text-center">
                            <div class="alert alert-info mb-0">
                                <i class="bi bi-info-circle"></i> No hay ventas registradas
                            </div>
                        </td>
                    </tr>
                    <?php else: ?>
                        <?php foreach ($ventas as $venta): ?>
                        <tr>
                            <td><code>#<?= $venta['id'] ?></code></td>
                            <td><?= date('d/m/Y H:i', strtotime($venta['fecha'])) ?></td>
                            <td>
                                <?php if ($venta['cliente_id']): ?>
                                    <strong><?= htmlspecialchars($venta['cliente_nombre'] . ' ' . $venta['cliente_apellido']) ?></strong>
                                <?php else: ?>
                                    <span class="text-muted">Cliente general</span>
                                <?php endif; ?>
                            </td>
                            <td><?= htmlspecialchars($venta['cajero_nombre']) ?></td>
                            <td>
                                <span class="badge bg-secondary">
                                    <?= $venta['total_productos'] ?> producto(s)
                                </span>
                            </td>
                            <td class="currency">
                                <strong>$<?= number_format($venta['total'], 0, ',', '.') ?></strong>
                            </td>
                            <td>
                                <?php
                                $icono_pago = [
                                    'efectivo' => 'cash',
                                    'tarjeta' => 'credit-card',
                                    'transferencia' => 'bank'
                                ];
                                $medio = $venta['medio_pago'];
                                ?>
                                <i class="bi bi-<?= $icono_pago[$medio] ?? 'cash' ?>"></i>
                                <?= ucfirst($medio) ?>
                            </td>
                            <td>
                                <span class="badge bg-<?= $venta['estado'] === 'completada' ? 'success' : ($venta['estado'] === 'cancelada' ? 'danger' : 'warning') ?>">
                                    <?= ucfirst($venta['estado']) ?>
                                </span>
                            </td>
                            <td>
                                <a href="/ventas/detalle/<?= $venta['id'] ?>" class="btn btn-sm btn-info" title="Ver detalle">
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
    jakake.searchTable('searchInput', 'ventasTable');
});
</script>

<?php require_once __DIR__ . '/../layouts/footer.php'; ?>
