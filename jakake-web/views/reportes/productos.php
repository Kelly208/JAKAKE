<?php require_once __DIR__ . '/../layouts/header.php'; ?>
<?php require_once __DIR__ . '/../layouts/sidebar.php'; ?>

<nav aria-label="breadcrumb">
    <ol class="breadcrumb">
        <li class="breadcrumb-item"><a href="/dashboard">Dashboard</a></li>
        <li class="breadcrumb-item"><a href="/reportes">Reportes</a></li>
        <li class="breadcrumb-item active">Productos</li>
    </ol>
</nav>

<div class="d-flex justify-content-between align-items-center mb-4">
    <h1 class="h2">
        <i class="bi bi-boxes"></i> Reporte de Productos
    </h1>
    <button onclick="window.print()" class="btn btn-primary">
        <i class="bi bi-printer"></i> Imprimir
    </button>
</div>

<!-- Filtros de Fecha -->
<div class="card shadow mb-4">
    <div class="card-header">
        <h6 class="m-0 font-weight-bold text-primary">
            <i class="bi bi-calendar-range"></i> Filtrar Productos Más Vendidos
        </h6>
    </div>
    <div class="card-body">
        <form method="GET" action="/reportes/productos" class="row g-3">
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

<!-- Resumen de Inventario -->
<div class="row mb-4">
    <div class="col-md-3">
        <div class="card shadow">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <p class="text-muted mb-1 small">Total Productos</p>
                        <h3 class="mb-0"><?= number_format($resumen_inventario['total_productos'] ?? 0) ?></h3>
                    </div>
                    <div class="text-primary">
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
                        <p class="text-muted mb-1 small">Unidades Totales</p>
                        <h3 class="mb-0"><?= number_format($resumen_inventario['unidades_totales'] ?? 0) ?></h3>
                    </div>
                    <div class="text-success">
                        <i class="bi bi-boxes" style="font-size: 2rem;"></i>
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
                        <p class="text-muted mb-1 small">Valor Inventario</p>
                        <h4 class="mb-0 currency">$<?= number_format($resumen_inventario['valor_inventario'] ?? 0, 0, ',', '.') ?></h4>
                    </div>
                    <div class="text-warning">
                        <i class="bi bi-currency-dollar" style="font-size: 2rem;"></i>
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
                        <p class="text-muted mb-1 small">Con Bajo Stock</p>
                        <h3 class="mb-0 text-danger"><?= number_format($resumen_inventario['productos_bajo_stock'] ?? 0) ?></h3>
                    </div>
                    <div class="text-danger">
                        <i class="bi bi-exclamation-triangle" style="font-size: 2rem;"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="row">
    <div class="col-lg-8">
        <!-- Productos Más Vendidos -->
        <div class="card shadow mb-4">
            <div class="card-header">
                <h6 class="m-0 font-weight-bold text-primary">
                    <i class="bi bi-trophy"></i> Productos Más Vendidos (Período Seleccionado)
                </h6>
            </div>
            <div class="card-body">
                <?php if (empty($productos_mas_vendidos)): ?>
                <div class="alert alert-info">
                    <i class="bi bi-info-circle"></i> No hay productos vendidos en este período
                </div>
                <?php else: ?>
                <div class="table-responsive">
                    <table class="table table-hover">
                        <thead class="table-light">
                            <tr>
                                <th>#</th>
                                <th>Código</th>
                                <th>Producto</th>
                                <th>Cantidad Vendida</th>
                                <th>Total Generado</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($productos_mas_vendidos as $index => $producto): ?>
                            <tr>
                                <td>
                                    <?php if ($index < 3): ?>
                                        <span class="badge bg-warning text-dark">#<?= $index + 1 ?></span>
                                    <?php else: ?>
                                        <?= $index + 1 ?>
                                    <?php endif; ?>
                                </td>
                                <td><code><?= htmlspecialchars($producto['codigo']) ?></code></td>
                                <td><?= htmlspecialchars($producto['producto']) ?></td>
                                <td><span class="badge bg-success"><?= $producto['cantidad_vendida'] ?></span></td>
                                <td class="currency">$<?= number_format($producto['total_generado'], 0, ',', '.') ?></td>
                            </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
                <?php endif; ?>
            </div>
        </div>

        <!-- Productos por Tipo -->
        <div class="card shadow mb-4">
            <div class="card-header">
                <h6 class="m-0 font-weight-bold text-primary">
                    <i class="bi bi-collection"></i> Análisis por Tipo de Producto
                </h6>
            </div>
            <div class="card-body">
                <?php if (empty($productos_por_tipo)): ?>
                <div class="alert alert-info">
                    <i class="bi bi-info-circle"></i> No hay datos disponibles
                </div>
                <?php else: ?>
                <div class="table-responsive">
                    <table class="table table-hover">
                        <thead class="table-light">
                            <tr>
                                <th>Tipo</th>
                                <th>Cantidad Productos</th>
                                <th>Stock Total</th>
                                <th>Precio Promedio</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($productos_por_tipo as $tipo): ?>
                            <tr>
                                <td><span class="badge bg-secondary"><?= ucfirst($tipo['tipo']) ?></span></td>
                                <td><?= $tipo['cantidad'] ?></td>
                                <td><?= number_format($tipo['stock_total']) ?> unidades</td>
                                <td class="currency">$<?= number_format($tipo['precio_promedio'], 0, ',', '.') ?></td>
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
        <!-- Productos con Bajo Stock -->
        <div class="card shadow mb-4 border-danger">
            <div class="card-header bg-danger text-white">
                <h6 class="m-0 font-weight-bold">
                    <i class="bi bi-exclamation-triangle"></i> Alertas de Stock
                </h6>
            </div>
            <div class="card-body">
                <?php if (empty($productos_bajo_stock)): ?>
                <div class="alert alert-success small">
                    <i class="bi bi-check-circle"></i> No hay productos con bajo stock
                </div>
                <?php else: ?>
                <div class="list-group list-group-flush">
                    <?php foreach ($productos_bajo_stock as $producto): ?>
                    <div class="list-group-item px-0">
                        <div class="d-flex justify-content-between align-items-start">
                            <div class="flex-grow-1">
                                <h6 class="mb-1"><?= htmlspecialchars($producto['nombre']) ?></h6>
                                <p class="mb-1 small text-muted">
                                    <code><?= htmlspecialchars($producto['codigo']) ?></code>
                                </p>
                                <p class="mb-0">
                                    <span class="badge bg-danger"><?= $producto['cantidad'] ?> unidades</span>
                                    <small class="text-muted">/ Mínimo: <?= $producto['cantidad_minima'] ?></small>
                                </p>
                            </div>
                        </div>
                    </div>
                    <?php endforeach; ?>
                </div>
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
                    <strong>Período analizado:</strong><br>
                    <?= date('d/m/Y', strtotime($fecha_inicio)) ?> al <?= date('d/m/Y', strtotime($fecha_fin)) ?>
                </p>
                <p class="small mb-2">
                    <strong>Estado del inventario:</strong><br>
                    Actualizado en tiempo real
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
