<?php require_once __DIR__ . '/../layouts/header.php'; ?>
<?php require_once __DIR__ . '/../layouts/sidebar.php'; ?>

<nav aria-label="breadcrumb">
    <ol class="breadcrumb">
        <li class="breadcrumb-item"><a href="/dashboard">Dashboard</a></li>
        <li class="breadcrumb-item"><a href="/reportes">Reportes</a></li>
        <li class="breadcrumb-item active">Clientes</li>
    </ol>
</nav>

<div class="d-flex justify-content-between align-items-center mb-4">
    <h1 class="h2">
        <i class="bi bi-person-lines-fill"></i> Reporte de Clientes
    </h1>
    <button onclick="window.print()" class="btn btn-primary">
        <i class="bi bi-printer"></i> Imprimir
    </button>
</div>

<!-- Resumen General -->
<div class="row mb-4">
    <div class="col-md-4">
        <div class="card shadow">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <p class="text-muted mb-1 small">Total Clientes</p>
                        <h3 class="mb-0"><?= number_format($resumen_clientes['total_clientes'] ?? 0) ?></h3>
                    </div>
                    <div class="text-primary">
                        <i class="bi bi-people" style="font-size: 2rem;"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-md-4">
        <div class="card shadow">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <p class="text-muted mb-1 small">Clientes Activos</p>
                        <h3 class="mb-0"><?= number_format($resumen_clientes['clientes_activos'] ?? 0) ?></h3>
                    </div>
                    <div class="text-success">
                        <i class="bi bi-person-check" style="font-size: 2rem;"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-md-4">
        <div class="card shadow">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <p class="text-muted mb-1 small">Clientes Inactivos</p>
                        <h3 class="mb-0"><?= number_format($resumen_clientes['clientes_inactivos'] ?? 0) ?></h3>
                    </div>
                    <div class="text-secondary">
                        <i class="bi bi-person-x" style="font-size: 2rem;"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="row">
    <div class="col-lg-8">
        <!-- Top Clientes -->
        <div class="card shadow mb-4">
            <div class="card-header">
                <h6 class="m-0 font-weight-bold text-primary">
                    <i class="bi bi-trophy"></i> Top 20 Clientes por Monto de Compras
                </h6>
            </div>
            <div class="card-body">
                <?php if (empty($top_clientes)): ?>
                <div class="alert alert-info">
                    <i class="bi bi-info-circle"></i> No hay datos de clientes con compras
                </div>
                <?php else: ?>
                <div class="table-responsive">
                    <table class="table table-hover">
                        <thead class="table-light">
                            <tr>
                                <th>#</th>
                                <th>Cédula</th>
                                <th>Cliente</th>
                                <th>Contacto</th>
                                <th>Compras</th>
                                <th>Monto Total</th>
                                <th>Última Compra</th>
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
                                <td><code><?= htmlspecialchars($cliente['cedula']) ?></code></td>
                                <td><?= htmlspecialchars($cliente['nombre'] . ' ' . $cliente['apellido']) ?></td>
                                <td>
                                    <small>
                                        <?php if ($cliente['email']): ?>
                                        <i class="bi bi-envelope"></i> <?= htmlspecialchars($cliente['email']) ?><br>
                                        <?php endif; ?>
                                        <?php if ($cliente['telefono']): ?>
                                        <i class="bi bi-telephone"></i> <?= htmlspecialchars($cliente['telefono']) ?>
                                        <?php endif; ?>
                                    </small>
                                </td>
                                <td><span class="badge bg-info"><?= $cliente['total_compras'] ?></span></td>
                                <td class="currency">$<?= number_format($cliente['monto_total'], 0, ',', '.') ?></td>
                                <td><?= date('d/m/Y', strtotime($cliente['ultima_compra'])) ?></td>
                            </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
                <?php endif; ?>
            </div>
        </div>

        <!-- Clientes Nuevos -->
        <div class="card shadow mb-4">
            <div class="card-header">
                <h6 class="m-0 font-weight-bold text-primary">
                    <i class="bi bi-person-plus"></i> Clientes Nuevos (Últimos 30 Días)
                </h6>
            </div>
            <div class="card-body">
                <?php if (empty($clientes_nuevos)): ?>
                <div class="alert alert-info">
                    <i class="bi bi-info-circle"></i> No hay clientes nuevos en los últimos 30 días
                </div>
                <?php else: ?>
                <div class="table-responsive">
                    <table class="table table-hover">
                        <thead class="table-light">
                            <tr>
                                <th>Cédula</th>
                                <th>Cliente</th>
                                <th>Fecha de Registro</th>
                                <th>Compras Realizadas</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($clientes_nuevos as $cliente): ?>
                            <tr>
                                <td><code><?= htmlspecialchars($cliente['cedula']) ?></code></td>
                                <td><?= htmlspecialchars($cliente['nombre'] . ' ' . $cliente['apellido']) ?></td>
                                <td><?= date('d/m/Y', strtotime($cliente['fecha_registro'])) ?></td>
                                <td>
                                    <?php if ($cliente['total_compras'] > 0): ?>
                                        <span class="badge bg-success"><?= $cliente['total_compras'] ?></span>
                                    <?php else: ?>
                                        <span class="badge bg-secondary">Sin compras</span>
                                    <?php endif; ?>
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

    <div class="col-lg-4">
        <!-- Clientes Inactivos -->
        <div class="card shadow mb-4 border-warning">
            <div class="card-header bg-warning text-dark">
                <h6 class="m-0 font-weight-bold">
                    <i class="bi bi-exclamation-triangle"></i> Clientes Inactivos (+90 días)
                </h6>
            </div>
            <div class="card-body">
                <?php if (empty($clientes_inactivos)): ?>
                <div class="alert alert-success small">
                    <i class="bi bi-check-circle"></i> No hay clientes inactivos
                </div>
                <?php else: ?>
                <div class="list-group list-group-flush">
                    <?php foreach ($clientes_inactivos as $cliente): ?>
                    <div class="list-group-item px-0">
                        <h6 class="mb-1"><?= htmlspecialchars($cliente['nombre'] . ' ' . $cliente['apellido']) ?></h6>
                        <p class="mb-1 small"><code><?= htmlspecialchars($cliente['cedula']) ?></code></p>
                        <?php if ($cliente['email']): ?>
                        <p class="mb-1 small text-muted">
                            <i class="bi bi-envelope"></i> <?= htmlspecialchars($cliente['email']) ?>
                        </p>
                        <?php endif; ?>
                        <p class="mb-0">
                            <span class="badge bg-warning text-dark">
                                <?= $cliente['dias_inactivo'] ?> días inactivo
                            </span>
                        </p>
                        <small class="text-muted">
                            Última compra: <?= date('d/m/Y', strtotime($cliente['ultima_compra'])) ?>
                        </small>
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
                    <strong>Criterios:</strong>
                </p>
                <ul class="small mb-2">
                    <li>Top clientes: por monto total</li>
                    <li>Nuevos: últimos 30 días</li>
                    <li>Inactivos: +90 días sin compras</li>
                </ul>
                <p class="small mb-0">
                    <strong>Generado:</strong><br>
                    <?= date('d/m/Y H:i:s') ?>
                </p>
            </div>
        </div>
    </div>
</div>

<?php require_once __DIR__ . '/../layouts/footer.php'; ?>
