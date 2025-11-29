<?php require_once __DIR__ . '/../layouts/header.php'; ?>
<?php require_once __DIR__ . '/../layouts/sidebar.php'; ?>

<nav aria-label="breadcrumb">
    <ol class="breadcrumb">
        <li class="breadcrumb-item"><a href="/dashboard">Dashboard</a></li>
        <li class="breadcrumb-item"><a href="/proveedores">Proveedores</a></li>
        <li class="breadcrumb-item active">Nuevo Proveedor</li>
    </ol>
</nav>

<h1 class="h2 mb-4">
    <i class="bi bi-plus-circle"></i> Nuevo Proveedor
</h1>

<div class="row">
    <div class="col-lg-8">
        <div class="card shadow">
            <div class="card-header">
                <h6 class="m-0 font-weight-bold text-primary">
                    <i class="bi bi-truck"></i> Información del Proveedor
                </h6>
            </div>
            <div class="card-body">
                <form method="POST" action="/proveedores/crear" id="formProveedor">
                    <input type="hidden" name="csrf_token" value="<?= \App\Utils\Security::generateToken() ?>">
                    
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="nit" class="form-label">NIT <span class="text-danger">*</span></label>
                            <input type="text" class="form-control" id="nit" name="nit" required>
                            <small class="text-muted">Número de identificación tributaria</small>
                        </div>

                        <div class="col-md-6 mb-3">
                            <label for="nombre" class="form-label">Nombre <span class="text-danger">*</span></label>
                            <input type="text" class="form-control" id="nombre" name="nombre" required>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="telefono" class="form-label">Teléfono <span class="text-danger">*</span></label>
                            <input type="tel" class="form-control" id="telefono" name="telefono" required>
                        </div>

                        <div class="col-md-6 mb-3">
                            <label for="email" class="form-label">Email <span class="text-danger">*</span></label>
                            <input type="email" class="form-control" id="email" name="email" required>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="contacto" class="form-label">Persona de Contacto</label>
                            <input type="text" class="form-control" id="contacto" name="contacto">
                            <small class="text-muted">Nombre del encargado de ventas</small>
                        </div>

                        <div class="col-md-6 mb-3">
                            <label for="estado" class="form-label">Estado</label>
                            <select class="form-select" id="estado" name="estado">
                                <option value="activo" selected>Activo</option>
                                <option value="inactivo">Inactivo</option>
                            </select>
                        </div>
                    </div>

                    <div class="mb-3">
                        <label for="direccion" class="form-label">Dirección <span class="text-danger">*</span></label>
                        <textarea class="form-control" id="direccion" name="direccion" rows="2" required></textarea>
                    </div>

                    <hr class="my-4">

                    <div class="d-flex justify-content-between">
                        <a href="/proveedores" class="btn btn-secondary">
                            <i class="bi bi-arrow-left"></i> Cancelar
                        </a>
                        <button type="submit" class="btn btn-primary">
                            <i class="bi bi-save"></i> Guardar Proveedor
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
                    <i class="bi bi-lightbulb text-warning"></i> El NIT debe ser único para cada proveedor.
                </p>
                <hr>
                <p class="text-muted small mb-0">
                    <i class="bi bi-truck text-info"></i> Los proveedores activos pueden ser asignados a productos.
                </p>
            </div>
        </div>
    </div>
</div>

<?php require_once __DIR__ . '/../layouts/footer.php'; ?>
