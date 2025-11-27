<?php require_once __DIR__ . '/../layouts/header.php'; ?>
<?php require_once __DIR__ . '/../layouts/sidebar.php'; ?>

<nav aria-label="breadcrumb">
    <ol class="breadcrumb">
        <li class="breadcrumb-item"><a href="/dashboard">Dashboard</a></li>
        <li class="breadcrumb-item"><a href="/productos">Productos</a></li>
        <li class="breadcrumb-item active">Nuevo Producto</li>
    </ol>
</nav>

<h1 class="h2 mb-4">
    <i class="bi bi-plus-circle"></i> Nuevo Producto
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
                <form method="POST" action="/productos/crear" id="formProducto">
                    <input type="hidden" name="csrf_token" value="<?= \App\Utils\Security::generateToken() ?>">
                    
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="codigo" class="form-label">Código <span class="text-danger">*</span></label>
                            <input type="text" class="form-control" id="codigo" name="codigo" required>
                            <small class="text-muted">Código único del producto</small>
                        </div>

                        <div class="col-md-6 mb-3">
                            <label for="tipo" class="form-label">Tipo <span class="text-danger">*</span></label>
                            <select class="form-select" id="tipo" name="tipo" required>
                                <option value="">Seleccione...</option>
                                <option value="escolar">Escolar</option>
                                <option value="oficina">Oficina</option>
                                <option value="arte">Arte</option>
                                <option value="otro">Otro</option>
                            </select>
                        </div>
                    </div>

                    <div class="mb-3">
                        <label for="nombre" class="form-label">Nombre <span class="text-danger">*</span></label>
                        <input type="text" class="form-control" id="nombre" name="nombre" required>
                    </div>

                    <div class="mb-3">
                        <label for="descripcion" class="form-label">Descripción</label>
                        <textarea class="form-control" id="descripcion" name="descripcion" rows="3"></textarea>
                    </div>

                    <div class="row">
                        <div class="col-md-4 mb-3">
                            <label for="precio" class="form-label">Precio <span class="text-danger">*</span></label>
                            <div class="input-group">
                                <span class="input-group-text">$</span>
                                <input type="number" class="form-control" id="precio" name="precio" min="0" step="100" required>
                            </div>
                        </div>

                        <div class="col-md-4 mb-3">
                            <label for="cantidad" class="form-label">Cantidad Inicial <span class="text-danger">*</span></label>
                            <input type="number" class="form-control" id="cantidad" name="cantidad" min="0" value="0" required>
                        </div>

                        <div class="col-md-4 mb-3">
                            <label for="cantidad_minima" class="form-label">Stock Mínimo <span class="text-danger">*</span></label>
                            <input type="number" class="form-control" id="cantidad_minima" name="cantidad_minima" min="1" value="10" required>
                        </div>
                    </div>

                    <div class="mb-3">
                        <label for="proveedor_id" class="form-label">Proveedor <span class="text-danger">*</span></label>
                        <select class="form-select" id="proveedor_id" name="proveedor_id" required>
                            <option value="">Seleccione un proveedor...</option>
                            <?php foreach ($proveedores as $proveedor): ?>
                            <option value="<?= $proveedor['id'] ?>">
                                <?= htmlspecialchars($proveedor['nombre']) ?> (<?= htmlspecialchars($proveedor['nit']) ?>)
                            </option>
                            <?php endforeach; ?>
                        </select>
                    </div>

                    <hr>

                    <div class="d-flex justify-content-between">
                        <a href="/productos" class="btn btn-secondary">
                            <i class="bi bi-arrow-left"></i> Cancelar
                        </a>
                        <button type="submit" class="btn btn-primary">
                            <i class="bi bi-save"></i> Guardar Producto
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
                    <i class="bi bi-info-circle"></i> Información
                </h6>
            </div>
            <div class="card-body">
                <p class="text-muted small mb-0">
                    <i class="bi bi-check-circle text-success"></i> Los campos marcados con <span class="text-danger">*</span> son obligatorios.
                </p>
                <hr>
                <p class="text-muted small mb-0">
                    <i class="bi bi-lightbulb text-warning"></i> El código debe ser único para cada producto.
                </p>
                <hr>
                <p class="text-muted small mb-0">
                    <i class="bi bi-bell text-info"></i> Se generará una alerta cuando el stock sea menor al mínimo.
                </p>
            </div>
        </div>
    </div>
</div>

<?php require_once __DIR__ . '/../layouts/footer.php'; ?>
