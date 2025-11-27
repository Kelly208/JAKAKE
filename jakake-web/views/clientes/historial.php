<?php require_once __DIR__ . '/../layouts/header.php'; ?>
<?php require_once __DIR__ . '/../layouts/sidebar.php'; ?>

<nav aria-label="breadcrumb">
    <ol class="breadcrumb">
        <li class="breadcrumb-item"><a href="/dashboard">Dashboard</a></li>
        <li class="breadcrumb-item"><a href="/clientes">Clientes</a></li>
        <li class="breadcrumb-item active">Historial de Compras</li>
    </ol>
</nav>

<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h1 class="h2 mb-0">
            <i class="bi bi-clock-history"></i> Historial de Compras
        </h1>
        <p class="text-muted">
            <strong><?= htmlspecialchars($cliente['nombre'] . ' ' . $cliente['apellido']) ?></strong>
            <span class="badge bg-secondary"><?= htmlspecialchars($cliente['cedula']) ?></span>
        </p>
    </div>
    <a href="/clientes" class="btn btn-secondary">
        <i class="bi bi-arrow-left"></i> Volver
    </a>
</div>

<div class="row mb-4">
    <div class="col-md-3">
        <div class="card shadow">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <p class="text-muted mb-1 small">Total Compras</p>
                        <h3 class="mb-0"><?= count($historial) ?></h3>
                    </div>
                    <div class="text-primary">
                        <i class="bi bi-cart-check" style="font-size: 2rem;"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card shadow">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <p class="text-muted mb-1 small">Total Gastado</p>
                        <h4 class="mb-0 currency">$<?= number_format(array_sum(array_column($historial, 'total')), 0, ',', '.') ?></h4>
                    </div>
                    <div class="text-success">
                        <i class="bi bi-cash-coin" style="font-size: 2rem;"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card shadow">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <p class="text-muted mb-1 small">Productos Comprados</p>
                        <h3 class="mb-0"><?= array_sum(array_column($historial, 'total_productos')) ?></h3>
                    </div>
                    <div class="text-info">
                        <i class="bi bi-box-seam" style="font-size: 2rem;"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card shadow">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <p class="text-muted mb-1 small">Estado</p>
                        <h5 class="mb-0">
                            <span class="badge bg-<?= $cliente['estado'] === 'activo' ? 'success' : 'secondary' ?>">
                                <?= ucfirst($cliente['estado']) ?>
                            </span>
                        </h5>
                    </div>
                    <div class="text-warning">
                        <i class="bi bi-person-check" style="font-size: 2rem;"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="card shadow">
    <div class="card-header">
        <h6 class="m-0 font-weight-bold text-primary">
            <i class="bi bi-list-ul"></i> Detalle de Compras
        </h6>
    </div>
    <div class="card-body">
        <?php if (empty($historial)): ?>
        <div class="alert alert-info">
            <i class="bi bi-info-circle"></i> Este cliente aún no ha realizado compras
        </div>
        <?php else: ?>
        <div class="table-responsive">
            <table class="table table-hover">
                <thead class="table-light">
                    <tr>
                        <th>ID</th>
                        <th>Fecha</th>
                        <th>Productos</th>
                        <th>Total</th>
                        <th>Medio de Pago</th>
                        <th>Cajero</th>
                        <th>Estado</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($historial as $venta): ?>
                    <tr>
                        <td><code>#<?= $venta['id'] ?></code></td>
                        <td><?= date('d/m/Y H:i', strtotime($venta['fecha'])) ?></td>
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
                        <td><?= htmlspecialchars($venta['nombre_cajero']) ?></td>
                        <td>
                            <span class="badge bg-<?= $venta['estado'] === 'completada' ? 'success' : ($venta['estado'] === 'cancelada' ? 'danger' : 'warning') ?>">
                                <?= ucfirst($venta['estado']) ?>
                            </span>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
        <?php endif; ?>
    </div>
</div>

<?php require_once __DIR__ . '/../layouts/footer.php'; ?>
