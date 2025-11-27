<?php require_once __DIR__ . '/../layouts/header.php'; ?>
<?php require_once __DIR__ . '/../layouts/sidebar.php'; ?>

<nav aria-label="breadcrumb">
    <ol class="breadcrumb">
        <li class="breadcrumb-item"><a href="/dashboard">Dashboard</a></li>
        <li class="breadcrumb-item"><a href="/productos">Productos</a></li>
        <li class="breadcrumb-item active">Editar Producto</li>
    </ol>
</nav>

<h1 class="h2 mb-4">
    <i class="bi bi-pencil"></i> Editar Producto
    <small class="text-muted">#<?= $producto['id'] ?></small>
</h1>

<div class="row">
    <div class="col-lg-8">
        <div class="card shadow">
            <div class="card-header">
                <h6 class="m-0 font-weight-bold text-primary">
                    <i class="bi bi-box-seam"></i> Información del Producto
                </h6>
            </div>
            <div class="card-body">
                <form method="POST" action="/productos/editar/<?= $producto['id'] ?>">
                    <input type="hidden" name="csrf_token" value="<?= \App\Utils\Security::generateToken() ?>">
                    
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="codigo" class="form-label">Código</label>
                            <input type="text" class="form-control" id="codigo" value="<?= htmlspecialchars($producto['codigo']) ?>" disabled>
                            <small class="text-muted">El código no se puede modificar</small>
                        </div>

                        <div class="col-md-6 mb-3">
                            <label for="tipo" class="form-label">Tipo <span class="text-danger">*</span></label>
                            <select class="form-select" id="tipo" name="tipo" required>
                                <option value="escolar" <?= $producto['tipo'] === 'escolar' ? 'selected' : '' ?>>Escolar</option>
                                <option value="oficina" <?= $producto['tipo'] === 'oficina' ? 'selected' : '' ?>>Oficina</option>
                                <option value="arte" <?= $producto['tipo'] === 'arte' ? 'selected' : '' ?>>Arte</option>
                                <option value="otro" <?= $producto['tipo'] === 'otro' ? 'selected' : '' ?>>Otro</option>
                            </select>
                        </div>
                    </div>

                    <div class="mb-3">
                        <label for="nombre" class="form-label">Nombre <span class="text-danger">*</span></label>
                        <input type="text" class="form-control" id="nombre" name="nombre" 
                               value="<?= htmlspecialchars($producto['nombre']) ?>" required>
                    </div>

                    <div class="mb-3">
                        <label for="descripcion" class="form-label">Descripción</label>
                        <textarea class="form-control" id="descripcion" name="descripcion" rows="3"><?= htmlspecialchars($producto['descripcion'] ?? '') ?></textarea>
                    </div>

                    <div class="row">
                        <div class="col-md-4 mb-3">
                            <label for="precio" class="form-label">Precio <span class="text-danger">*</span></label>
                            <div class="input-group">
                                <span class="input-group-text">$</span>
                                <input type="number" class="form-control" id="precio" name="precio" 
                                       value="<?= $producto['precio'] ?>" min="0" step="100" required>
                            </div>
                        </div>

                        <div class="col-md-4 mb-3">
                            <label for="cantidad" class="form-label">Cantidad <span class="text-danger">*</span></label>
                            <input type="number" class="form-control" id="cantidad" name="cantidad" 
                                   value="<?= $producto['cantidad'] ?>" min="0" required>
                        </div>

                        <div class="col-md-4 mb-3">
                            <label for="cantidad_minima" class="form-label">Stock Mínimo <span class="text-danger">*</span></label>
                            <input type="number" class="form-control" id="cantidad_minima" name="cantidad_minima" 
                                   value="<?= $producto['cantidad_minima'] ?>" min="1" required>
                        </div>
                    </div>

                    <div class="mb-3">
                        <label for="proveedor_id" class="form-label">Proveedor <span class="text-danger">*</span></label>
                        <select class="form-select" id="proveedor_id" name="proveedor_id" required>
                            <option value="">Seleccione un proveedor...</option>
                            <?php foreach ($proveedores as $proveedor): ?>
                            <option value="<?= $proveedor['id'] ?>" 
                                    <?= $producto['proveedor_id'] == $proveedor['id'] ? 'selected' : '' ?>>
                                <?= htmlspecialchars($proveedor['nombre']) ?> (<?= htmlspecialchars($proveedor['nit']) ?>)
                            </option>
                            <?php endforeach; ?>
                        </select>
                    </div>

                    <div class="mb-3">
                        <label for="estado" class="form-label">Estado <span class="text-danger">*</span></label>
                        <select class="form-select" id="estado" name="estado" required>
                            <option value="activo" <?= $producto['estado'] === 'activo' ? 'selected' : '' ?>>Activo</option>
                            <option value="inactivo" <?= $producto['estado'] === 'inactivo' ? 'selected' : '' ?>>Inactivo</option>
                        </select>
                        <small class="text-muted">Los productos inactivos no aparecerán en las ventas</small>
                    </div>

                    <hr>

                    <div class="d-flex justify-content-between">
                        <a href="/productos" class="btn btn-secondary">
                            <i class="bi bi-arrow-left"></i> Cancelar
                        </a>
                        <button type="submit" class="btn btn-primary">
                            <i class="bi bi-save"></i> Guardar Cambios
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <div class="col-lg-4">
        <div class="card shadow mb-4">
            <div class="card-header">
                <h6 class="m-0 font-weight-bold text-primary">
                    <i class="bi bi-clock-history"></i> Historial
                </h6>
            </div>
            <div class="card-body">
                <p class="text-muted small mb-2">
                    <strong>Fecha de ingreso:</strong><br>
                    <?= date('d/m/Y H:i', strtotime($producto['fecha_ingreso'])) ?>
                </p>
                <?php if ($producto['fecha_actualizacion']): ?>
                <p class="text-muted small mb-0">
                    <strong>Última actualización:</strong><br>
                    <?= date('d/m/Y H:i', strtotime($producto['fecha_actualizacion'])) ?>
                </p>
                <?php endif; ?>
            </div>
        </div>

        <div class="card shadow">
            <div class="card-header">
                <h6 class="m-0 font-weight-bold text-primary">
                    <i class="bi bi-info-circle"></i> Estado del Stock
                </h6>
            </div>
            <div class="card-body">
                <?php if ($producto['cantidad'] < $producto['cantidad_minima']): ?>
                    <div class="alert alert-danger mb-0">
                        <i class="bi bi-exclamation-triangle-fill"></i> 
                        <strong>Stock bajo</strong><br>
                        <small>Cantidad actual: <?= $producto['cantidad'] ?></small><br>
                        <small>Mínimo requerido: <?= $producto['cantidad_minima'] ?></small>
                    </div>
                <?php elseif ($producto['cantidad'] < $producto['cantidad_minima'] * 2): ?>
                    <div class="alert alert-warning mb-0">
                        <i class="bi bi-exclamation-circle-fill"></i> 
                        <strong>Stock moderado</strong><br>
                        <small>Considere reabastecer pronto</small>
                    </div>
                <?php else: ?>
                    <div class="alert alert-success mb-0">
                        <i class="bi bi-check-circle-fill"></i> 
                        <strong>Stock óptimo</strong><br>
                        <small>Cantidad suficiente en inventario</small>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>

<?php require_once __DIR__ . '/../layouts/footer.php'; ?>
