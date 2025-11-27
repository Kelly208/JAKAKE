<?php require_once __DIR__ . '/../layouts/header.php'; ?>
<?php require_once __DIR__ . '/../layouts/sidebar.php'; ?>

<nav aria-label="breadcrumb">
    <ol class="breadcrumb">
        <li class="breadcrumb-item"><a href="/dashboard">Dashboard</a></li>
        <li class="breadcrumb-item"><a href="/usuarios">Usuarios</a></li>
        <li class="breadcrumb-item active">Nuevo Usuario</li>
    </ol>
</nav>

<h1 class="h2 mb-4">
    <i class="bi bi-person-plus"></i> Nuevo Usuario
</h1>

<div class="row">
    <div class="col-lg-8">
        <div class="card shadow">
            <div class="card-header">
                <h6 class="m-0 font-weight-bold text-primary">
                    <i class="bi bi-person"></i> Información del Usuario
                </h6>
            </div>
            <div class="card-body">
                <form method="POST" action="/usuarios/crear" id="formUsuario">
                    <input type="hidden" name="csrf_token" value="<?= \App\Utils\Security::generateToken() ?>">
                    
                    <div class="mb-3">
                        <label for="nombre" class="form-label">Nombre Completo <span class="text-danger">*</span></label>
                        <input type="text" class="form-control" id="nombre" name="nombre" required>
                    </div>

                    <div class="mb-3">
                        <label for="email" class="form-label">Email <span class="text-danger">*</span></label>
                        <input type="email" class="form-control" id="email" name="email" required>
                        <small class="text-muted">Se usará para iniciar sesión</small>
                    </div>

                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="password" class="form-label">Contraseña <span class="text-danger">*</span></label>
                            <input type="password" class="form-control" id="password" name="password" required minlength="6">
                            <small class="text-muted">Mínimo 6 caracteres</small>
                        </div>

                        <div class="col-md-6 mb-3">
                            <label for="confirmar_password" class="form-label">Confirmar Contraseña <span class="text-danger">*</span></label>
                            <input type="password" class="form-control" id="confirmar_password" required minlength="6">
                        </div>
                    </div>

                    <div class="mb-3">
                        <label for="rol" class="form-label">Rol <span class="text-danger">*</span></label>
                        <select class="form-select" id="rol" name="rol" required>
                            <option value="">Seleccione un rol...</option>
                            <option value="administrador">Administrador</option>
                            <option value="cajero">Cajero</option>
                        </select>
                        <small class="text-muted">
                            <strong>Administrador:</strong> Acceso completo al sistema<br>
                            <strong>Cajero:</strong> Solo puede realizar ventas y devoluciones
                        </small>
                    </div>

                    <hr>

                    <div class="d-flex justify-content-between">
                        <a href="/usuarios" class="btn btn-secondary">
                            <i class="bi bi-arrow-left"></i> Cancelar
                        </a>
                        <button type="submit" class="btn btn-primary">
                            <i class="bi bi-save"></i> Crear Usuario
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
                    <i class="bi bi-shield-lock text-warning"></i> La contraseña será encriptada de forma segura.
                </p>
                <hr>
                <p class="text-muted small mb-0">
                    <i class="bi bi-envelope text-info"></i> El email debe ser único en el sistema.
                </p>
            </div>
        </div>

        <div class="card shadow">
            <div class="card-header">
                <h6 class="m-0 font-weight-bold text-primary">
                    <i class="bi bi-shield-check"></i> Permisos por Rol
                </h6>
            </div>
            <div class="card-body">
                <h6 class="text-warning">Administrador</h6>
                <ul class="small mb-3">
                    <li>Gestión de productos</li>
                    <li>Gestión de clientes</li>
                    <li>Ventas y devoluciones</li>
                    <li>Reportes completos</li>
                    <li>Gestión de usuarios</li>
                </ul>

                <h6 class="text-info">Cajero</h6>
                <ul class="small mb-0">
                    <li>Realizar ventas</li>
                    <li>Procesar devoluciones</li>
                    <li>Ver productos y clientes</li>
                    <li>Dashboard básico</li>
                </ul>
            </div>
        </div>
    </div>
</div>

<script>
document.getElementById('formUsuario').addEventListener('submit', function(e) {
    const password = document.getElementById('password').value;
    const confirmar = document.getElementById('confirmar_password').value;

    if (password !== confirmar) {
        e.preventDefault();
        jakake.showToast('Las contraseñas no coinciden', 'danger');
        return false;
    }

    if (password.length < 6) {
        e.preventDefault();
        jakake.showToast('La contraseña debe tener al menos 6 caracteres', 'danger');
        return false;
    }
});
</script>

<?php require_once __DIR__ . '/../layouts/footer.php'; ?>
