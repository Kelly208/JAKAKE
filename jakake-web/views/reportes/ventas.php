<?php 
// Debug temporal
if (empty($resumen['total_ventas'])) {
    echo "<!-- DEBUG: resumen vacío - total_ventas=" . ($resumen['total_ventas'] ?? 'undefined') . " -->";
}
?>
<?php require_once __DIR__ . '/../layouts/header.php'; ?>
<?php require_once __DIR__ . '/../layouts/sidebar.php'; ?>

<nav aria-label="breadcrumb">
    <ol class="breadcrumb">
        <li class="breadcrumb-item"><a href="/dashboard">Dashboard</a></li>
        <li class="breadcrumb-item"><a href="/reportes">Reportes</a></li>
        <li class="breadcrumb-item active">Ventas</li>
    </ol>
</nav>

<div class="d-flex justify-content-between align-items-center mb-4">
    <h1 class="h2">
        <i class="bi bi-graph-up"></i> Reporte de Ventas
    </h1>
    <button onclick="window.print()" class="btn btn-primary">
        <i class="bi bi-printer"></i> Imprimir
    </button>
</div>

<!-- Filtros de Fecha -->
<div class="card shadow mb-4">
    <div class="card-header">
        <h6 class="m-0 font-weight-bold text-primary">
            <i class="bi bi-calendar-range"></i> Filtrar por Período
        </h6>
    </div>
    <div class="card-body">
        <form method="GET" action="/reportes/ventas" class="row g-3">
            <div class="col-md-4">
                <label for="fecha_inicio" class="form-label">Fecha Inicio</label>
                <input type="date" class="form-control" id="fecha_inicio" name="fecha_inicio" 
                       value="<?= htmlspecialchars($fecha_inicio) ?>" required>
            </div>
            <div class="col-md-4">
                <label for="fecha_fin" class="form-label">Fecha Fin</label>
                <input type="date" class="form-control" id="fecha_fin" name="fecha_fin" 
                       value="<?= htmlspecialchars($fecha_fin) ?>" required>
            </div>
            <div class="col-md-4">
                <label class="form-label">&nbsp;</label>
                <button type="submit" class="btn btn-primary w-100">
                    <i class="bi bi-search"></i> Generar Reporte
                </button>
            </div>
        </form>
    </div>
</div>

<!-- Resumen General -->
<div class="row mb-4">
    <div class="col-md-3">
        <div class="card shadow">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <p class="text-muted mb-1 small">Total Ventas</p>
                        <h3 class="mb-0"><?= number_format($resumen['total_ventas'] ?? 0) ?></h3>
                    </div>
                    <div class="text-primary">
                        <i class="bi bi-receipt" style="font-size: 2rem;"></i>
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
                        <p class="text-muted mb-1 small">Monto Total</p>
                        <h4 class="mb-0 currency">$<?= number_format($resumen['monto_total'] ?? 0, 0, ',', '.') ?></h4>
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
                        <p class="text-muted mb-1 small">Promedio por Venta</p>
                        <h4 class="mb-0 currency">$<?= number_format($resumen['promedio_venta'] ?? 0, 0, ',', '.') ?></h4>
                    </div>
                    <div class="text-warning">
                        <i class="bi bi-calculator" style="font-size: 2rem;"></i>
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
                        <p class="text-muted mb-1 small">Clientes Únicos</p>
                        <h3 class="mb-0"><?= number_format($resumen['clientes_unicos'] ?? 0) ?></h3>
                    </div>
                    <div class="text-info">
                        <i class="bi bi-people" style="font-size: 2rem;"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="row">
    <div class="col-lg-8">
        <!-- Ventas por Día -->
        <div class="card shadow mb-4">
            <div class="card-header">
                <h6 class="m-0 font-weight-bold text-primary">
                    <i class="bi bi-calendar-day"></i> Ventas por Día
                </h6>
            </div>
            <div class="card-body">
                <?php if (empty($ventas_diarias)): ?>
                <div class="alert alert-info">
                    <i class="bi bi-info-circle"></i> No hay ventas en el período seleccionado
                </div>
                <?php else: ?>
                <div class="table-responsive">
                    <table class="table table-hover">
                        <thead class="table-light">
                            <tr>
                                <th>Fecha</th>
                                <th>Cantidad de Ventas</th>
                                <th>Monto Total</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($ventas_diarias as $venta): ?>
                            <tr>
                                <td><?= date('d/m/Y', strtotime($venta['fecha'])) ?></td>
                                <td><span class="badge bg-primary"><?= $venta['cantidad_ventas'] ?></span></td>
                                <td class="currency">$<?= number_format($venta['monto_total'], 0, ',', '.') ?></td>
                            </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
                <?php endif; ?>
            </div>
        </div>

        <!-- Top 10 Clientes -->
        <div class="card shadow mb-4">
            <div class="card-header">
                <h6 class="m-0 font-weight-bold text-primary">
                    <i class="bi bi-trophy"></i> Top 10 Clientes del Período
                </h6>
            </div>
            <div class="card-body">
                <?php if (empty($top_clientes)): ?>
                <div class="alert alert-info">
                    <i class="bi bi-info-circle"></i> No hay datos de clientes en este período
                </div>
                <?php else: ?>
                <div class="table-responsive">
                    <table class="table table-hover">
                        <thead class="table-light">
                            <tr>
                                <th>#</th>
                                <th>Cliente</th>
                                <th>Cédula</th>
                                <th>Compras</th>
                                <th>Monto Total</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($top_clientes as $index => $cliente): ?>
                            <tr>
                                <td>
                                    <?php if ($index < 3): ?>
                                        <span class="badge bg-warning text-dark">#<?= $index + 1 ?></span>
                                    <?php else: ?>
                                        <?= $index + 1 ?>
                                    <?php endif; ?>
                                </td>
                                <td><?= htmlspecialchars($cliente['nombre']) ?></td>
                                <td><code><?= htmlspecialchars($cliente['cedula']) ?></code></td>
                                <td><?= $cliente['total_compras'] ?></td>
                                <td class="currency">$<?= number_format($cliente['monto_total'], 0, ',', '.') ?></td>
                            </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
                <?php endif; ?>
            </div>
        </div>
    </div>

    <div class="col-lg-4">
        <!-- Ventas por Medio de Pago -->
        <div class="card shadow mb-4">
            <div class="card-header">
                <h6 class="m-0 font-weight-bold text-primary">
                    <i class="bi bi-credit-card"></i> Medios de Pago
                </h6>
            </div>
            <div class="card-body">
                <?php if (empty($ventas_por_medio)): ?>
                <div class="alert alert-info small">
                    <i class="bi bi-info-circle"></i> Sin datos
                </div>
                <?php else: ?>
                    <?php foreach ($ventas_por_medio as $medio): ?>
                    <div class="mb-3">
                        <div class="d-flex justify-content-between mb-1">
                            <span>
                                <?php
                                $iconos = ['efectivo' => 'cash', 'tarjeta' => 'credit-card', 'transferencia' => 'bank'];
                                ?>
                                <i class="bi bi-<?= $iconos[$medio['medio_pago']] ?? 'cash' ?>"></i>
                                <?= ucfirst($medio['medio_pago']) ?>
                            </span>
                            <span class="text-muted"><?= $medio['porcentaje'] ?>%</span>
                        </div>
                        <div class="progress" style="height: 20px;">
                            <div class="progress-bar" role="progressbar" 
                                 style="width: <?= $medio['porcentaje'] ?>%">
                                <?= $medio['cantidad'] ?> ventas
                            </div>
                        </div>
                        <small class="text-muted">$<?= number_format($medio['monto_total'], 0, ',', '.') ?></small>
                    </div>
                    <?php endforeach; ?>
                <?php endif; ?>
            </div>
        </div>

        <!-- Información del Reporte -->
        <div class="card shadow">
            <div class="card-header bg-info text-white">
                <h6 class="m-0 font-weight-bold">
                    <i class="bi bi-info-circle"></i> Información del Reporte
                </h6>
            </div>
            <div class="card-body">
                <p class="small mb-2">
                    <strong>Período:</strong><br>
                    <?= date('d/m/Y', strtotime($fecha_inicio)) ?> al <?= date('d/m/Y', strtotime($fecha_fin)) ?>
                </p>
                <p class="small mb-2">
                    <strong>Días analizados:</strong><br>
                    <?= (strtotime($fecha_fin) - strtotime($fecha_inicio)) / (60 * 60 * 24) + 1 ?> días
                </p>
                <p class="small mb-0">
                    <strong>Generado:</strong><br>
                    <?= date('d/m/Y H:i:s') ?>
                </p>
            </div>
        </div>
    </div>
</div>

<?php require_once __DIR__ . '/../layouts/footer.php'; ?>
