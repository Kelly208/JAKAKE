<?php require_once __DIR__ . '/../layouts/header.php'; ?>
<?php require_once __DIR__ . '/../layouts/sidebar.php'; ?>

<nav aria-label="breadcrumb">
    <ol class="breadcrumb">
        <li class="breadcrumb-item"><a href="/dashboard">Dashboard</a></li>
        <li class="breadcrumb-item"><a href="/usuarios">Usuarios</a></li>
        <li class="breadcrumb-item active">Editar Usuario</li>
    </ol>
</nav>

<h1 class="h2 mb-4">
    <i class="bi bi-pencil"></i> Editar Usuario
    <small class="text-muted">#<?= $usuario['id'] ?></small>
</h1>

<div class="row">
    <div class="col-lg-8">
        <div class="card shadow mb-4">
            <div class="card-header">
                <h6 class="m-0 font-weight-bold text-primary">
                    <i class="bi bi-person"></i> Información del Usuario
                </h6>
            </div>
            <div class="card-body">
                <form method="POST" action="/usuarios/editar/<?= $usuario['id'] ?>">
                    <input type="hidden" name="csrf_token" value="<?= \App\Utils\Security::generateToken() ?>">
                    
                    <div class="mb-3">
                        <label for="nombre" class="form-label">Nombre Completo <span class="text-danger">*</span></label>
                        <input type="text" class="form-control" id="nombre" name="nombre" 
                               value="<?= htmlspecialchars($usuario['nombre']) ?>" required>
                    </div>

                    <div class="mb-3">
                        <label for="email" class="form-label">Email <span class="text-danger">*</span></label>
                        <input type="email" class="form-control" id="email" name="email" 
                               value="<?= htmlspecialchars($usuario['email']) ?>" required>
                    </div>

                    <div class="mb-3">
                        <label for="rol" class="form-label">Rol <span class="text-danger">*</span></label>
                        <select class="form-select" id="rol" name="rol" required>
                            <option value="administrador" <?= $usuario['rol'] === 'administrador' ? 'selected' : '' ?>>
                                Administrador
                            </option>
                            <option value="cajero" <?= $usuario['rol'] === 'cajero' ? 'selected' : '' ?>>
                                Cajero
                            </option>
                        </select>
                        <?php if ($usuario['id'] == Session::get('user_id')): ?>
                            <small class="text-warning">
                                <i class="bi bi-exclamation-triangle"></i> No puedes cambiar tu propio rol
                            </small>
                        <?php endif; ?>
                    </div>

                    <div class="mb-3">
                        <label for="estado" class="form-label">Estado <span class="text-danger">*</span></label>
                        <select class="form-select" id="estado" name="estado" required>
                            <option value="activo" <?= $usuario['estado'] === 'activo' ? 'selected' : '' ?>>
                                Activo
                            </option>
                            <option value="inactivo" <?= $usuario['estado'] === 'inactivo' ? 'selected' : '' ?>>
                                Inactivo
                            </option>
                        </select>
                        <?php if ($usuario['id'] == Session::get('user_id')): ?>
                            <small class="text-warning">
                                <i class="bi bi-exclamation-triangle"></i> No puedes desactivar tu propia cuenta
                            </small>
                        <?php endif; ?>
                        <small class="text-muted d-block">Los usuarios inactivos no podrán iniciar sesión</small>
                    </div>

                    <hr>

                    <div class="d-flex justify-content-between">
                        <a href="/usuarios" class="btn btn-secondary">
                            <i class="bi bi-arrow-left"></i> Cancelar
                        </a>
                        <button type="submit" class="btn btn-primary">
                            <i class="bi bi-save"></i> Guardar Cambios
                        </button>
                    </div>
                </form>
            </div>
        </div>

        <!-- Cambiar Contraseña -->
        <div class="card shadow">
            <div class="card-header bg-warning text-dark">
                <h6 class="m-0 font-weight-bold">
                    <i class="bi bi-key"></i> Cambiar Contraseña
                </h6>
            </div>
            <div class="card-body">
                <form method="POST" action="/usuarios/reset-password/<?= $usuario['id'] ?>" id="formPassword">
                    <input type="hidden" name="csrf_token" value="<?= \App\Utils\Security::generateToken() ?>">
                    
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="nueva_password" class="form-label">Nueva Contraseña</label>
                            <input type="password" class="form-control" id="nueva_password" 
                                   name="nueva_password" minlength="6">
                        </div>

                        <div class="col-md-6 mb-3">
                            <label for="confirmar_password" class="form-label">Confirmar Contraseña</label>
                            <input type="password" class="form-control" id="confirmar_password" minlength="6">
                        </div>
                    </div>

                    <button type="submit" class="btn btn-warning">
                        <i class="bi bi-key"></i> Actualizar Contraseña
                    </button>
                </form>
            </div>
        </div>
    </div>

    <div class="col-lg-4">
        <div class="card shadow mb-4">
            <div class="card-header">
                <h6 class="m-0 font-weight-bold text-primary">
                    <i class="bi bi-clock-history"></i> Información
                </h6>
            </div>
            <div class="card-body">
                <p class="text-muted small mb-2">
                    <strong>Fecha de creación:</strong><br>
                    <?= date('d/m/Y H:i', strtotime($usuario['fecha_creacion'])) ?>
                </p>
                <?php if ($usuario['ultimo_login']): ?>
                <p class="text-muted small mb-0">
                    <strong>Último login:</strong><br>
                    <?= date('d/m/Y H:i', strtotime($usuario['ultimo_login'])) ?>
                </p>
                <?php else: ?>
                <p class="text-muted small mb-0">
                    <strong>Último login:</strong><br>
                    <em>Nunca ha iniciado sesión</em>
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
                <p class="text-muted small mb-2">
                    <strong>Total de ventas:</strong><br>
                    <span class="h4 text-primary"><?= $estadisticas['total_ventas'] ?></span>
                </p>
                <p class="text-muted small mb-0">
                    <strong>Monto total generado:</strong><br>
                    <span class="h5 text-success">$<?= number_format($estadisticas['monto_total_ventas'], 0, ',', '.') ?></span>
                </p>
            </div>
        </div>
    </div>
</div>

<script>
document.getElementById('formPassword').addEventListener('submit', function(e) {
    const nueva = document.getElementById('nueva_password').value;
    const confirmar = document.getElementById('confirmar_password').value;

    if (!nueva || !confirmar) {
        e.preventDefault();
        jakake.showToast('Complete ambos campos de contraseña', 'warning');
        return false;
    }

    if (nueva !== confirmar) {
        e.preventDefault();
        jakake.showToast('Las contraseñas no coinciden', 'danger');
        return false;
    }

    if (nueva.length < 6) {
        e.preventDefault();
        jakake.showToast('La contraseña debe tener al menos 6 caracteres', 'danger');
        return false;
    }
});
</script>

<?php require_once __DIR__ . '/../layouts/footer.php'; ?>
