<?php
use App\Utils\Session;
require_once __DIR__ . '/../layouts/header.php';
require_once __DIR__ . '/../layouts/sidebar.php';
?>

<nav aria-label="breadcrumb">
    <ol class="breadcrumb">
        <li class="breadcrumb-item"><a href="/dashboard">Dashboard</a></li>
        <li class="breadcrumb-item active">Usuarios</li>
    </ol>
</nav>

<div class="d-flex justify-content-between align-items-center mb-4">
    <h1 class="h2">
        <i class="bi bi-people-fill"></i> Gestión de Usuarios
    </h1>
    <a href="/usuarios/crear" class="btn btn-primary">
        <i class="bi bi-plus-circle"></i> Nuevo Usuario
    </a>
</div>

<?php if (Session::get('success')): ?>
<div class="alert alert-success alert-dismissible fade show">
    <?= Session::get('success') ?>
    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
</div>
<?php Session::delete('success'); endif; ?>

<?php if (Session::get('error')): ?>
<div class="alert alert-danger alert-dismissible fade show">
    <?= Session::get('error') ?>
    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
</div>
<?php Session::delete('error'); endif; ?>

<div class="row mb-4">
    <div class="col-md-4">
        <div class="card shadow">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <p class="text-muted mb-1 small">Total Usuarios</p>
                        <h3 class="mb-0"><?= count($usuarios) ?></h3>
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
                        <p class="text-muted mb-1 small">Administradores</p>
                        <h3 class="mb-0"><?= count(array_filter($usuarios, fn($u) => $u['rol'] === 'administrador')) ?></h3>
                    </div>
                    <div class="text-warning">
                        <i class="bi bi-shield-fill-check" style="font-size: 2rem;"></i>
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
                        <p class="text-muted mb-1 small">Cajeros</p>
                        <h3 class="mb-0"><?= count(array_filter($usuarios, fn($u) => $u['rol'] === 'cajero')) ?></h3>
                    </div>
                    <div class="text-info">
                        <i class="bi bi-person-badge" style="font-size: 2rem;"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="card shadow">
    <div class="card-header">
        <div class="row align-items-center">
            <div class="col">
                <h6 class="m-0 font-weight-bold text-primary">
                    <i class="bi bi-list-ul"></i> Listado de Usuarios
                </h6>
            </div>
            <div class="col-auto">
                <input type="text" id="searchInput" class="form-control" placeholder="Buscar usuarios...">
            </div>
        </div>
    </div>
    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-hover" id="usuariosTable">
                <thead class="table-light">
                    <tr>
                        <th>ID</th>
                        <th>Nombre</th>
                        <th>Email</th>
                        <th>Rol</th>
                        <th>Estado</th>
                        <th>Ventas Realizadas</th>
                        <th>Fecha Creación</th>
                        <th>Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (empty($usuarios)): ?>
                    <tr>
                        <td colspan="8" class="text-center">
                            <div class="alert alert-info mb-0">
                                <i class="bi bi-info-circle"></i> No hay usuarios registrados
                            </div>
                        </td>
                    </tr>
                    <?php else: ?>
                        <?php foreach ($usuarios as $usuario): ?>
                        <tr>
                            <td><code>#<?= $usuario['id'] ?></code></td>
                            <td>
                                <strong><?= htmlspecialchars($usuario['nombre']) ?></strong>
                                <?php if ($usuario['id'] == Session::get('user_id')): ?>
                                    <span class="badge bg-info ms-1">Tú</span>
                                <?php endif; ?>
                            </td>
                            <td><?= htmlspecialchars($usuario['email']) ?></td>
                            <td>
                                <?php if ($usuario['rol'] === 'administrador'): ?>
                                    <span class="badge bg-warning text-dark">
                                        <i class="bi bi-shield-fill-check"></i> Administrador
                                    </span>
                                <?php else: ?>
                                    <span class="badge bg-info">
                                        <i class="bi bi-person-badge"></i> Cajero
                                    </span>
                                <?php endif; ?>
                            </td>
                            <td>
                                <span class="badge bg-<?= $usuario['estado'] === 'activo' ? 'success' : 'secondary' ?>">
                                    <?= ucfirst($usuario['estado']) ?>
                                </span>
                            </td>
                            <td>
                                <span class="badge bg-primary"><?= $usuario['total_ventas'] ?></span>
                            </td>
                            <td><?= date('d/m/Y', strtotime($usuario['fecha_creacion'])) ?></td>
                            <td>
                                <a href="/usuarios/editar/<?= $usuario['id'] ?>" class="btn btn-sm btn-primary" title="Editar">
                                    <i class="bi bi-pencil"></i>
                                </a>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    jakake.searchTable('searchInput', 'usuariosTable');
});
</script>

<?php require_once __DIR__ . '/../layouts/footer.php'; ?>
