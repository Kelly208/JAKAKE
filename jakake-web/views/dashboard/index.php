<?php require_once __DIR__ . '/../layouts/header.php'; ?>
<?php require_once __DIR__ . '/../layouts/sidebar.php'; ?>

<!-- Breadcrumb -->
<nav aria-label="breadcrumb">
    <ol class="breadcrumb">
        <li class="breadcrumb-item active">Dashboard</li>
    </ol>
</nav>

<h1 class="h2 mb-4">
    <i class="bi bi-speedometer2"></i> Dashboard
    <small class="text-muted">Resumen del día</small>
</h1>

<!-- Tarjetas de estadísticas -->
<div class="row mb-4">
    <!-- Ventas del día -->
    <div class="col-xl-3 col-md-6 mb-4">
        <div class="card stat-card shadow h-100 py-2">
            <div class="card-body">
                <div class="row no-gutters align-items-center">
                    <div class="col mr-2">
                        <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">
                            Ventas Hoy
                        </div>
                        <div class="h5 mb-0 font-weight-bold text-gray-800">
                            <?= $stats['ventas_hoy'] ?>
                        </div>
                    </div>
                    <div class="col-auto">
                        <i class="bi bi-cart-check stat-icon text-primary"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Total del día -->
    <div class="col-xl-3 col-md-6 mb-4">
        <div class="card stat-card success shadow h-100 py-2">
            <div class="card-body">
                <div class="row no-gutters align-items-center">
                    <div class="col mr-2">
                        <div class="text-xs font-weight-bold text-success text-uppercase mb-1">
                            Total del Día
                        </div>
                        <div class="h5 mb-0 font-weight-bold text-gray-800 currency">
                            <?= number_format($stats['total_dia'], 0, ',', '.') ?>
                        </div>
                    </div>
                    <div class="col-auto">
                        <i class="bi bi-currency-dollar stat-icon text-success"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Total productos -->
    <div class="col-xl-3 col-md-6 mb-4">
        <div class="card stat-card shadow h-100 py-2">
            <div class="card-body">
                <div class="row no-gutters align-items-center">
                    <div class="col mr-2">
                        <div class="text-xs font-weight-bold text-info text-uppercase mb-1">
                            Productos Activos
                        </div>
                        <div class="h5 mb-0 font-weight-bold text-gray-800">
                            <?= $stats['total_productos'] ?>
                        </div>
                    </div>
                    <div class="col-auto">
                        <i class="bi bi-box-seam stat-icon text-info"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Productos bajo stock -->
    <div class="col-xl-3 col-md-6 mb-4">
        <div class="card stat-card <?= $stats['productos_bajo_stock'] > 0 ? 'danger' : '' ?> shadow h-100 py-2">
            <div class="card-body">
                <div class="row no-gutters align-items-center">
                    <div class="col mr-2">
                        <div class="text-xs font-weight-bold text-warning text-uppercase mb-1">
                            Stock Bajo
                        </div>
                        <div class="h5 mb-0 font-weight-bold text-gray-800">
                            <?= $stats['productos_bajo_stock'] ?>
                        </div>
                    </div>
                    <div class="col-auto">
                        <i class="bi bi-exclamation-triangle stat-icon text-warning"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Contenido principal -->
<div class="row">
    <!-- Ventas recientes -->
    <div class="col-lg-8 mb-4">
        <div class="card shadow">
            <div class="card-header py-3 d-flex justify-content-between align-items-center">
                <h6 class="m-0 font-weight-bold text-primary">
                    <i class="bi bi-clock-history"></i> Ventas Recientes
                </h6>
                <a href="/ventas" class="btn btn-sm btn-primary">Ver todas</a>
            </div>
            <div class="card-body">
                <?php if (empty($ventasRecientes)): ?>
                    <div class="alert alert-info">
                        <i class="bi bi-info-circle"></i> No hay ventas registradas hoy.
                    </div>
                <?php else: ?>
                    <div class="table-responsive">
                        <table class="table table-hover">
                            <thead class="table-light">
                                <tr>
                                    <th>ID</th>
                                    <th>Fecha</th>
                                    <th>Cliente</th>
                                    <th>Cajero</th>
                                    <th>Total</th>
                                    <th>Estado</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($ventasRecientes as $venta): ?>
                                <tr>
                                    <td><strong>#<?= $venta['id'] ?></strong></td>
                                    <td><?= date('d/m/Y H:i', strtotime($venta['fecha'])) ?></td>
                                    <td><?= htmlspecialchars($venta['cliente_nombre'] ?? 'Sin cliente') ?></td>
                                    <td><?= htmlspecialchars($venta['usuario_nombre']) ?></td>
                                    <td class="currency">$<?= number_format($venta['total'], 0, ',', '.') ?></td>
                                    <td>
                                        <span class="badge bg-<?= $venta['estado'] === 'completada' ? 'success' : 'danger' ?>">
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
    </div>

    <!-- Productos más vendidos -->
    <div class="col-lg-4 mb-4">
        <div class="card shadow">
            <div class="card-header py-3">
                <h6 class="m-0 font-weight-bold text-primary">
                    <i class="bi bi-trophy"></i> Top Productos (30 días)
                </h6>
            </div>
            <div class="card-body">
                <?php if (empty($topProductos)): ?>
                    <div class="alert alert-info">
                        <i class="bi bi-info-circle"></i> No hay datos disponibles.
                    </div>
                <?php else: ?>
                    <ul class="list-group list-group-flush">
                        <?php foreach ($topProductos as $index => $producto): ?>
                        <li class="list-group-item d-flex justify-content-between align-items-center">
                            <div>
                                <span class="badge bg-primary rounded-pill me-2"><?= $index + 1 ?></span>
                                <strong><?= htmlspecialchars($producto['producto']) ?></strong>
                                <br>
                                <small class="text-muted">
                                    <?= $producto['cantidad_vendida'] ?> unidades
                                </small>
                            </div>
                            <span class="badge bg-success rounded-pill currency">
                                $<?= number_format($producto['total_vendido'], 0, ',', '.') ?>
                            </span>
                        </li>
                        <?php endforeach; ?>
                    </ul>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>

<!-- Productos bajo stock -->
<?php if (!empty($productosStock)): ?>
<div class="row">
    <div class="col-12 mb-4">
        <div class="card shadow border-warning">
            <div class="card-header bg-warning text-dark py-3 d-flex justify-content-between align-items-center">
                <h6 class="m-0 font-weight-bold">
                    <i class="bi bi-exclamation-triangle-fill"></i> Alerta: Productos Bajo Stock
                </h6>
                <a href="/productos" class="btn btn-sm btn-dark">Gestionar productos</a>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-hover">
                        <thead class="table-light">
                            <tr>
                                <th>Código</th>
                                <th>Producto</th>
                                <th>Stock Actual</th>
                                <th>Stock Mínimo</th>
                                <th>Proveedor</th>
                                <th>Acción</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($productosStock as $producto): ?>
                            <tr>
                                <td><code><?= htmlspecialchars($producto['codigo']) ?></code></td>
                                <td><strong><?= htmlspecialchars($producto['nombre']) ?></strong></td>
                                <td>
                                    <span class="badge bg-danger"><?= $producto['cantidad'] ?></span>
                                </td>
                                <td><?= $producto['cantidad_minima'] ?></td>
                                <td><?= htmlspecialchars($producto['proveedor']) ?></td>
                                <td>
                                    <a href="/productos/editar/<?= $producto['id'] ?>" class="btn btn-sm btn-primary">
                                        <i class="bi bi-pencil"></i> Editar
                                    </a>
                                </td>
                            </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>
<?php endif; ?>

<!-- Acciones rápidas -->
<div class="row mb-4">
    <div class="col-12">
        <div class="card shadow">
            <div class="card-header py-3">
                <h6 class="m-0 font-weight-bold text-primary">
                    <i class="bi bi-lightning"></i> Acciones Rápidas
                </h6>
            </div>
            <div class="card-body">
                <div class="row text-center">
                    <div class="col-md-3 mb-3">
                        <a href="/ventas" class="btn btn-primary btn-lg w-100">
                            <i class="bi bi-cart-plus d-block mb-2" style="font-size: 2rem;"></i>
                            Nueva Venta
                        </a>
                    </div>
                    <div class="col-md-3 mb-3">
                        <a href="/productos" class="btn btn-info btn-lg w-100">
                            <i class="bi bi-box-seam d-block mb-2" style="font-size: 2rem;"></i>
                            Ver Productos
                        </a>
                    </div>
                    <div class="col-md-3 mb-3">
                        <a href="/clientes" class="btn btn-success btn-lg w-100">
                            <i class="bi bi-people d-block mb-2" style="font-size: 2rem;"></i>
                            Ver Clientes
                        </a>
                    </div>
                    <div class="col-md-3 mb-3">
                        <a href="/reportes" class="btn btn-secondary btn-lg w-100">
                            <i class="bi bi-graph-up d-block mb-2" style="font-size: 2rem;"></i>
                            Reportes
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<?php require_once __DIR__ . '/../layouts/footer.php'; ?>
