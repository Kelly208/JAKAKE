<?php
use App\Utils\Session;
require_once __DIR__ . '/../layouts/header.php';
require_once __DIR__ . '/../layouts/sidebar.php';
?>

<nav aria-label="breadcrumb">
    <ol class="breadcrumb">
        <li class="breadcrumb-item"><a href="/dashboard">Dashboard</a></li>
        <li class="breadcrumb-item"><a href="/devoluciones">Devoluciones</a></li>
        <li class="breadcrumb-item active">Detalle de Devolución</li>
    </ol>
</nav>

<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h1 class="h2 mb-0">
            <i class="bi bi-arrow-counterclockwise"></i> Detalle de Devolución
            <span class="text-muted">#<?= $devolucion['id'] ?></span>
        </h1>
        <p class="text-muted mb-0">
            Fecha: <?= date('d/m/Y H:i', strtotime($devolucion['fecha_devolucion'])) ?>
        </p>
    </div>
    <div>
        <a href="/devoluciones" class="btn btn-secondary">
            <i class="bi bi-arrow-left"></i> Volver
        </a>
        <button onclick="window.print()" class="btn btn-primary">
            <i class="bi bi-printer"></i> Imprimir
        </button>
    </div>
</div>

<?php if (Session::get('success')): ?>
<div class="alert alert-success alert-dismissible fade show">
    <?= Session::get('success') ?>
    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
</div>
<?php Session::delete('success'); endif; ?>

<?php if (Session::get('codigo_bono')): ?>
<div class="alert alert-warning alert-dismissible fade show">
    <i class="bi bi-gift-fill"></i> <strong>Bono Regalo Generado:</strong> 
    <code class="fs-5"><?= Session::get('codigo_bono') ?></code>
    <br><small>Este bono puede ser utilizado en futuras compras</small>
</div>
<?php Session::delete('codigo_bono'); endif; ?>

<div class="row">
    <div class="col-lg-8">
        <!-- Información de Devolución -->
        <div class="card shadow mb-4">
            <div class="card-header">
                <h6 class="m-0 font-weight-bold text-primary">
                    <i class="bi bi-info-circle"></i> Información de la Devolución
                </h6>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-6">
                        <h6 class="text-muted mb-2">Cliente</h6>
                        <?php if ($devolucion['cliente_nombre']): ?>
                            <p class="mb-1">
                                <strong><?= htmlspecialchars($devolucion['cliente_nombre']) ?></strong>
                            </p>
                            <p class="mb-1 text-muted small">
                                <i class="bi bi-card-text"></i> <?= htmlspecialchars($devolucion['cliente_cedula']) ?>
                            </p>
                            <?php if ($devolucion['cliente_telefono']): ?>
                            <p class="mb-0 text-muted small">
                                <i class="bi bi-telephone"></i> <?= htmlspecialchars($devolucion['cliente_telefono']) ?>
                            </p>
                            <?php endif; ?>
                        <?php else: ?>
                            <p class="text-muted">Cliente general</p>
                        <?php endif; ?>
                    </div>
                    <div class="col-md-6">
                        <h6 class="text-muted mb-2">Información de Devolución</h6>
                        <p class="mb-1">
                            <strong>Venta original:</strong> 
                            <a href="/ventas/detalle/<?= $devolucion['venta_id'] ?>" class="text-primary">
                                #<?= $devolucion['venta_id'] ?>
                            </a>
                        </p>
                        <p class="mb-1">
                            <strong>Fecha venta:</strong> <?= date('d/m/Y', strtotime($devolucion['fecha_venta'])) ?>
                        </p>
                        <p class="mb-0">
                            <strong>Usuario:</strong> <?= htmlspecialchars($devolucion['usuario_nombre']) ?>
                        </p>
                    </div>
                </div>

                <hr>

                <div>
                    <h6 class="text-muted mb-2">Motivo de Devolución</h6>
                    <p class="mb-0"><?= nl2br(htmlspecialchars($devolucion['motivo'])) ?></p>
                </div>
            </div>
        </div>

        <!-- Productos Devueltos -->
        <div class="card shadow mb-4">
            <div class="card-header">
                <h6 class="m-0 font-weight-bold text-primary">
                    <i class="bi bi-box-seam"></i> Productos Devueltos
                </h6>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-hover">
                        <thead class="table-light">
                            <tr>
                                <th>Código</th>
                                <th>Producto</th>
                                <th>Precio Unit.</th>
                                <th>Cantidad</th>
                                <th>Subtotal</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php 
                            $total_devuelto = 0;
                            foreach ($detalles as $detalle): 
                                $subtotal_item = $detalle['valor_devolucion'];
                                $total_devuelto += $subtotal_item;
                            ?>
                            <tr>
                                <td><code><?= htmlspecialchars($detalle['producto_codigo']) ?></code></td>
                                <td><?= htmlspecialchars($detalle['producto_nombre']) ?></td>
                                <td class="currency">$<?= number_format($detalle['precio_unitario'], 0, ',', '.') ?></td>
                                <td><?= $detalle['cantidad'] ?></td>
                                <td class="currency">$<?= number_format($subtotal_item, 0, ',', '.') ?></td>
                            </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <div class="col-lg-4">
        <!-- Resumen de Totales -->
        <div class="card shadow mb-4">
            <div class="card-header bg-warning text-dark">
                <h6 class="m-0 font-weight-bold">
                    <i class="bi bi-calculator"></i> Resumen de Devolución
                </h6>
            </div>
            <div class="card-body">
                <div class="d-flex justify-content-between mb-2">
                    <span>Total productos:</span>
                    <span><?= array_sum(array_column($detalles, 'cantidad')) ?></span>
                </div>
                <hr>
                <div class="d-flex justify-content-between mb-3">
                    <strong>Monto devuelto:</strong>
                    <strong class="h4 text-warning currency">$<?= number_format($devolucion['monto_total'], 0, ',', '.') ?></strong>
                </div>
            </div>
        </div>

        <!-- Bono Regalo -->
        <?php if ($bono): ?>
        <div class="card shadow mb-4 border-warning">
            <div class="card-header bg-warning text-dark">
                <h6 class="m-0 font-weight-bold">
                    <i class="bi bi-gift-fill"></i> Bono Regalo Generado
                </h6>
            </div>
            <div class="card-body text-center">
                <div class="mb-3">
                    <i class="bi bi-gift" style="font-size: 3rem; color: #ffc107;"></i>
                </div>
                <h3 class="mb-2">
                    <code class="bg-light p-2 rounded"><?= htmlspecialchars($bono['codigo']) ?></code>
                </h3>
                <p class="text-muted mb-2">
                    <strong>Valor:</strong> $<?= number_format($bono['valor'], 0, ',', '.') ?>
                </p>
                <p class="text-muted small mb-2">
                    <strong>Estado:</strong> 
                    <span class="badge bg-<?= $bono['estado'] === 'activo' ? 'success' : ($bono['estado'] === 'usado' ? 'secondary' : 'danger') ?>">
                        <?= ucfirst($bono['estado']) ?>
                    </span>
                </p>
                <p class="text-muted small mb-0">
                    <strong>Válido hasta:</strong><br>
                    <?= date('d/m/Y', strtotime($bono['fecha_vencimiento'])) ?>
                </p>
                <hr>
                <p class="small text-muted mb-0">
                    Este bono puede ser utilizado en cualquier compra futura
                </p>
            </div>
        </div>
        <?php endif; ?>

        <!-- Información Adicional -->
        <div class="card shadow">
            <div class="card-header">
                <h6 class="m-0 font-weight-bold text-primary">
                    <i class="bi bi-calendar"></i> Información Adicional
                </h6>
            </div>
            <div class="card-body">
                <p class="text-muted small mb-2">
                    <strong>Fecha de devolución:</strong><br>
                    <?= date('d/m/Y H:i:s', strtotime($devolucion['fecha_devolucion'])) ?>
                </p>
                <p class="text-muted small mb-0">
                    <strong>ID de devolución:</strong> #<?= $devolucion['id'] ?>
                </p>
            </div>
        </div>
    </div>
</div>

<!-- Estilos de impresión -->
<style>
@media print {
    .sidebar, .navbar, .breadcrumb, .btn, nav {
        display: none !important;
    }
    .content {
        margin: 0 !important;
        padding: 20px !important;
    }
    .card {
        border: 1px solid #000;
        box-shadow: none !important;
    }
}
</style>

<?php require_once __DIR__ . '/../layouts/footer.php'; ?>
