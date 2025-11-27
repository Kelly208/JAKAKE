<?php require_once __DIR__ . '/../layouts/header.php'; ?>
<?php require_once __DIR__ . '/../layouts/sidebar.php'; ?>

<nav aria-label="breadcrumb">
    <ol class="breadcrumb">
        <li class="breadcrumb-item"><a href="/dashboard">Dashboard</a></li>
        <li class="breadcrumb-item"><a href="/clientes">Clientes</a></li>
        <li class="breadcrumb-item active">Editar Cliente</li>
    </ol>
</nav>

<h1 class="h2 mb-4">
    <i class="bi bi-pencil"></i> Editar Cliente
    <small class="text-muted"><?= htmlspecialchars($cliente['cedula']) ?></small>
</h1>

<div class="row">
    <div class="col-lg-8">
        <div class="card shadow mb-4">
            <div class="card-header">
                <h6 class="m-0 font-weight-bold text-primary">
                    <i class="bi bi-person"></i> Información del Cliente
                </h6>
            </div>
            <div class="card-body">
                <form method="POST" action="/clientes/editar/<?= $cliente['id'] ?>">
                    <input type="hidden" name="csrf_token" value="<?= \App\Utils\Security::generateToken() ?>">
                    
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="cedula" class="form-label">Cédula</label>
                            <input type="text" class="form-control" id="cedula" value="<?= htmlspecialchars($cliente['cedula']) ?>" disabled>
                            <small class="text-muted">La cédula no se puede modificar</small>
                        </div>

                        <div class="col-md-6 mb-3">
                            <label for="email" class="form-label">Email <span class="text-danger">*</span></label>
                            <input type="email" class="form-control" id="email" name="email" 
                                   value="<?= htmlspecialchars($cliente['email']) ?>" required>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="nombre" class="form-label">Nombre <span class="text-danger">*</span></label>
                            <input type="text" class="form-control" id="nombre" name="nombre" 
                                   value="<?= htmlspecialchars($cliente['nombre']) ?>" required>
                        </div>

                        <div class="col-md-6 mb-3">
                            <label for="apellido" class="form-label">Apellido <span class="text-danger">*</span></label>
                            <input type="text" class="form-control" id="apellido" name="apellido" 
                                   value="<?= htmlspecialchars($cliente['apellido']) ?>" required>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="telefono" class="form-label">Teléfono</label>
                            <input type="tel" class="form-control" id="telefono" name="telefono" 
                                   value="<?= htmlspecialchars($cliente['telefono'] ?? '') ?>">
                        </div>

                        <div class="col-md-6 mb-3">
                            <label for="direccion" class="form-label">Dirección</label>
                            <input type="text" class="form-control" id="direccion" name="direccion" 
                                   value="<?= htmlspecialchars($cliente['direccion'] ?? '') ?>">
                        </div>
                    </div>

                    <div class="mb-3">
                        <label for="estado" class="form-label">Estado <span class="text-danger">*</span></label>
                        <select class="form-select" id="estado" name="estado" required>
                            <option value="activo" <?= $cliente['estado'] === 'activo' ? 'selected' : '' ?>>Activo</option>
                            <option value="inactivo" <?= $cliente['estado'] === 'inactivo' ? 'selected' : '' ?>>Inactivo</option>
                        </select>
                        <small class="text-muted">Los clientes inactivos no podrán realizar compras</small>
                    </div>

                    <hr>

                    <div class="d-flex justify-content-between">
                        <a href="/clientes" class="btn btn-secondary">
                            <i class="bi bi-arrow-left"></i> Cancelar
                        </a>
                        <button type="submit" class="btn btn-primary">
                            <i class="bi bi-save"></i> Guardar Cambios
                        </button>
                    </div>
                </form>
            </div>
        </div>

        <?php if (!empty($historial)): ?>
        <div class="card shadow">
            <div class="card-header">
                <h6 class="m-0 font-weight-bold text-primary">
                    <i class="bi bi-clock-history"></i> Historial de Compras
                </h6>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-sm">
                        <thead class="table-light">
                            <tr>
                                <th>Fecha</th>
                                <th>Total</th>
                                <th>Productos</th>
                                <th>Estado</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($historial as $venta): ?>
                            <tr>
                                <td><?= date('d/m/Y', strtotime($venta['fecha'])) ?></td>
                                <td class="currency">$<?= number_format($venta['total'], 0, ',', '.') ?></td>
                                <td><?= $venta['total_productos'] ?> producto(s)</td>
                                <td>
                                    <span class="badge bg-<?= $venta['estado'] === 'completada' ? 'success' : 'warning' ?>">
                                        <?= ucfirst($venta['estado']) ?>
                                    </span>
                                </td>
                            </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
        <?php endif; ?>
    </div>

    <div class="col-lg-4">
        <div class="card shadow mb-4">
            <div class="card-header">
                <h6 class="m-0 font-weight-bold text-primary">
                    <i class="bi bi-shield-check"></i> Políticas de Datos
                </h6>
            </div>
            <div class="card-body">
                <?php if ($cliente['fecha_aceptacion']): ?>
                    <div class="alert alert-success mb-0">
                        <i class="bi bi-check-circle-fill"></i> 
                        <strong>Políticas aceptadas</strong><br>
                        <small>Fecha: <?= date('d/m/Y H:i', strtotime($cliente['fecha_aceptacion'])) ?></small><br>
                        <small>Versión: <?= htmlspecialchars($cliente['version_politica']) ?></small>
                    </div>
                <?php else: ?>
                    <div class="alert alert-warning mb-0">
                        <i class="bi bi-exclamation-triangle-fill"></i> 
                        <strong>Sin aceptación registrada</strong><br>
                        <small>Este cliente debe aceptar las políticas</small>
                    </div>
                <?php endif; ?>
            </div>
        </div>

        <div class="card shadow mb-4">
            <div class="card-header">
                <h6 class="m-0 font-weight-bold text-primary">
                    <i class="bi bi-calendar"></i> Fechas
                </h6>
            </div>
            <div class="card-body">
                <p class="text-muted small mb-2">
                    <strong>Fecha de registro:</strong><br>
                    <?= date('d/m/Y H:i', strtotime($cliente['fecha_registro'])) ?>
                </p>
                <?php if ($cliente['ultima_compra']): ?>
                <p class="text-muted small mb-0">
                    <strong>Última compra:</strong><br>
                    <?= date('d/m/Y H:i', strtotime($cliente['ultima_compra'])) ?>
                </p>
                <?php else: ?>
                <p class="text-muted small mb-0">
                    <strong>Última compra:</strong><br>
                    <em>Sin compras registradas</em>
                </p>
                <?php endif; ?>
            </div>
        </div>

        <div class="card shadow">
            <div class="card-header">
                <h6 class="m-0 font-weight-bold text-primary">
                    <i class="bi bi-graph-up"></i> Estadísticas
                </h6>
            </div>
            <div class="card-body">
                <?php
                $total_compras = count($historial);
                $total_gastado = array_sum(array_column($historial, 'total'));
                ?>
                <p class="text-muted small mb-2">
                    <strong>Total de compras:</strong> <?= $total_compras ?>
                </p>
                <p class="text-muted small mb-0">
                    <strong>Total gastado:</strong> $<?= number_format($total_gastado, 0, ',', '.') ?>
                </p>
            </div>
        </div>
    </div>
</div>

<?php require_once __DIR__ . '/../layouts/footer.php'; ?>
