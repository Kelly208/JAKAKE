<?php require_once __DIR__ . '/../layouts/header.php'; ?>
<?php require_once __DIR__ . '/../layouts/sidebar.php'; ?>

<nav aria-label="breadcrumb">
    <ol class="breadcrumb">
        <li class="breadcrumb-item"><a href="/dashboard">Dashboard</a></li>
        <li class="breadcrumb-item"><a href="/ventas">Ventas</a></li>
        <li class="breadcrumb-item active">Detalle de Venta</li>
    </ol>
</nav>

<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h1 class="h2 mb-0">
            <i class="bi bi-receipt"></i> Detalle de Venta
            <span class="text-muted">#<?= $venta['id'] ?></span>
        </h1>
        <p class="text-muted mb-0">
            Fecha: <?= date('d/m/Y H:i', strtotime($venta['fecha_registro'])) ?>
        </p>
    </div>
    <div>
        <a href="/ventas" class="btn btn-secondary">
            <i class="bi bi-arrow-left"></i> Volver
        </a>
        <button onclick="window.print()" class="btn btn-primary">
            <i class="bi bi-printer"></i> Imprimir
        </button>
    </div>
</div>

<div class="row">
    <div class="col-lg-8">
        <!-- Información de Cliente y Venta -->
        <div class="card shadow mb-4">
            <div class="card-header">
                <h6 class="m-0 font-weight-bold text-primary">
                    <i class="bi bi-info-circle"></i> Información de la Venta
                </h6>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-6">
                        <h6 class="text-muted mb-2">Cliente</h6>
                        <?php if ($venta['cliente_id']): ?>
                            <p class="mb-1">
                                <strong><?= htmlspecialchars($venta['cliente_nombre']) ?></strong>
                            </p>
                            <p class="mb-1 text-muted small">
                                <i class="bi bi-card-text"></i> <?= htmlspecialchars($venta['cliente_cedula']) ?>
                            </p>
                            <?php if ($venta['cliente_telefono']): ?>
                            <p class="mb-1 text-muted small">
                                <i class="bi bi-telephone"></i> <?= htmlspecialchars($venta['cliente_telefono']) ?>
                            </p>
                            <?php endif; ?>
                            <?php if ($venta['cliente_email']): ?>
                            <p class="mb-0 text-muted small">
                                <i class="bi bi-envelope"></i> <?= htmlspecialchars($venta['cliente_email']) ?>
                            </p>
                            <?php endif; ?>
                        <?php else: ?>
                            <p class="text-muted">Cliente general</p>
                        <?php endif; ?>
                    </div>
                    <div class="col-md-6">
                        <h6 class="text-muted mb-2">Información de Venta</h6>
                        <p class="mb-1">
                            <strong>Cajero:</strong> <?= htmlspecialchars($venta['cajero_nombre']) ?>
                        </p>
                        <p class="mb-1">
                            <strong>Medio de pago:</strong> 
                            <?php
                            $icono_pago = [
                                'efectivo' => 'cash',
                                'tarjeta' => 'credit-card',
                                'transferencia' => 'bank'
                            ];
                            ?>
                            <i class="bi bi-<?= $icono_pago[$venta['medio_pago']] ?? 'cash' ?>"></i>
                            <?= ucfirst($venta['medio_pago']) ?>
                        </p>
                        <p class="mb-0">
                            <strong>Estado:</strong>
                            <span class="badge bg-<?= $venta['estado'] === 'completada' ? 'success' : ($venta['estado'] === 'cancelada' ? 'danger' : 'warning') ?>">
                                <?= ucfirst($venta['estado']) ?>
                            </span>
                        </p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Detalle de Productos -->
        <div class="card shadow mb-4">
            <div class="card-header">
                <h6 class="m-0 font-weight-bold text-primary">
                    <i class="bi bi-box-seam"></i> Productos Vendidos
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
                            $subtotal = 0;
                            foreach ($detalles as $detalle): 
                                $subtotal_item = $detalle['precio_unitario'] * $detalle['cantidad'];
                                $subtotal += $subtotal_item;
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
            <div class="card-header bg-primary text-white">
                <h6 class="m-0 font-weight-bold">
                    <i class="bi bi-calculator"></i> Resumen de Totales
                </h6>
            </div>
            <div class="card-body">
                <?php
                $subtotal = array_sum(array_map(function($d) {
                    return $d['precio_unitario'] * $d['cantidad'];
                }, $detalles));
                $iva = $subtotal * 0.19;
                $total = $subtotal + $iva;
                ?>
                <div class="d-flex justify-content-between mb-2">
                    <span>Subtotal:</span>
                    <span class="currency">$<?= number_format($subtotal, 0, ',', '.') ?></span>
                </div>
                <div class="d-flex justify-content-between mb-2">
                    <span>IVA (19%):</span>
                    <span class="currency">$<?= number_format($iva, 0, ',', '.') ?></span>
                </div>
                <hr>
                <div class="d-flex justify-content-between mb-3">
                    <strong>Total:</strong>
                    <strong class="h4 text-primary currency">$<?= number_format($total, 0, ',', '.') ?></strong>
                </div>

                <div class="alert alert-info small mb-0">
                    <i class="bi bi-info-circle"></i> 
                    <strong>Total productos:</strong> <?= array_sum(array_column($detalles, 'cantidad')) ?>
                </div>
            </div>
        </div>

        <!-- Información Adicional -->
        <div class="card shadow">
            <div class="card-header">
                <h6 class="m-0 font-weight-bold text-primary">
                    <i class="bi bi-calendar"></i> Información Adicional
                </h6>
            </div>
            <div class="card-body">
                <p class="text-muted small mb-2">
                    <strong>Fecha de registro:</strong><br>
                    <?= date('d/m/Y H:i:s', strtotime($venta['fecha'])) ?>
                </p>
                <p class="text-muted small mb-0">
                    <strong>ID de venta:</strong> #<?= $venta['id'] ?>
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
