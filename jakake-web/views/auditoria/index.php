<?php
use App\Utils\Session;
require_once __DIR__ . '/../layouts/header.php';
require_once __DIR__ . '/../layouts/sidebar.php';
?>

<nav aria-label="breadcrumb">
    <ol class="breadcrumb">
        <li class="breadcrumb-item"><a href="/dashboard">Dashboard</a></li>
        <li class="breadcrumb-item active">Auditoría</li>
    </ol>
</nav>

<div class="d-flex justify-content-between align-items-center mb-4">
    <h1 class="h2">
        <i class="bi bi-clipboard-data"></i> Auditoría del Sistema
    </h1>
</div>

<div class="card shadow mb-4">
    <div class="card-header">
        <h6 class="m-0 font-weight-bold text-primary">
            <i class="bi bi-funnel"></i> Filtros
        </h6>
    </div>
    <div class="card-body">
        <form method="GET" action="/auditoria" class="row g-3">
            <div class="col-md-3">
                <label for="tabla" class="form-label">Tabla</label>
                <select class="form-select" id="tabla" name="tabla">
                    <option value="">Todas las tablas</option>
                    <?php foreach ($tablas as $t): ?>
                        <option value="<?= $t ?>" <?= ($tabla ?? '') === $t ? 'selected' : '' ?>>
                            <?= ucfirst($t) ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>
            
            <div class="col-md-3">
                <label for="operacion" class="form-label">Operación</label>
                <select class="form-select" id="operacion" name="operacion">
                    <option value="">Todas las operaciones</option>
                    <option value="INSERT" <?= ($operacion ?? '') === 'INSERT' ? 'selected' : '' ?>>INSERT</option>
                    <option value="UPDATE" <?= ($operacion ?? '') === 'UPDATE' ? 'selected' : '' ?>>UPDATE</option>
                    <option value="DELETE" <?= ($operacion ?? '') === 'DELETE' ? 'selected' : '' ?>>DELETE</option>
                </select>
            </div>
            
            <div class="col-md-2">
                <label for="fecha_desde" class="form-label">Desde</label>
                <input type="date" class="form-control" id="fecha_desde" name="fecha_desde" 
                       value="<?= $fecha_desde ?? '' ?>">
            </div>
            
            <div class="col-md-2">
                <label for="fecha_hasta" class="form-label">Hasta</label>
                <input type="date" class="form-control" id="fecha_hasta" name="fecha_hasta" 
                       value="<?= $fecha_hasta ?? '' ?>">
            </div>
            
            <div class="col-md-2 d-flex align-items-end">
                <button type="submit" class="btn btn-primary w-100">
                    <i class="bi bi-search"></i> Filtrar
                </button>
            </div>
        </form>
    </div>
</div>

<div class="card shadow">
    <div class="card-header">
        <div class="row align-items-center">
            <div class="col">
                <h6 class="m-0 font-weight-bold text-primary">
                    <i class="bi bi-list-ul"></i> Registros de Auditoría (Últimos 500)
                </h6>
            </div>
            <div class="col-auto">
                <span class="badge bg-info"><?= count($registros) ?> registros</span>
            </div>
        </div>
    </div>
    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-hover table-sm" id="auditoriaTable">
                <thead class="table-light">
                    <tr>
                        <th>Fecha/Hora</th>
                        <th>Tabla</th>
                        <th>Operación</th>
                        <th>Usuario</th>
                        <th>Registro ID</th>
                        <th>Detalles</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (empty($registros)): ?>
                    <tr>
                        <td colspan="6" class="text-center">
                            <div class="alert alert-info mb-0">
                                <i class="bi bi-info-circle"></i> No hay registros de auditoría con los filtros seleccionados
                            </div>
                        </td>
                    </tr>
                    <?php else: ?>
                        <?php foreach ($registros as $registro): ?>
                        <tr>
                            <td>
                                <small><?= date('d/m/Y H:i:s', strtotime($registro['fecha_hora'])) ?></small>
                            </td>
                            <td>
                                <span class="badge bg-secondary"><?= htmlspecialchars($registro['tabla_afectada']) ?></span>
                            </td>
                            <td>
                                <?php
                                $color = match($registro['operacion']) {
                                    'INSERT' => 'success',
                                    'UPDATE' => 'warning',
                                    'DELETE' => 'danger',
                                    default => 'secondary'
                                };
                                ?>
                                <span class="badge bg-<?= $color ?>"><?= $registro['operacion'] ?></span>
                            </td>
                            <td>
                                <?php if ($registro['usuario_nombre']): ?>
                                    <small>
                                        <i class="bi bi-person"></i> 
                                        <?= htmlspecialchars($registro['usuario_nombre']) ?>
                                        <br>
                                        <span class="text-muted"><?= htmlspecialchars($registro['usuario_email']) ?></span>
                                    </small>
                                <?php else: ?>
                                    <small class="text-muted">Sistema</small>
                                <?php endif; ?>
                            </td>
                            <td>
                                <code><?= $registro['registro_id'] ?></code>
                            </td>
                            <td>
                                <?php if ($registro['valores_anteriores'] || $registro['valores_nuevos']): ?>
                                <button type="button" class="btn btn-sm btn-info" 
                                        data-bs-toggle="modal" 
                                        data-bs-target="#detalleModal<?= $registro['id'] ?>">
                                    <i class="bi bi-eye"></i> Ver
                                </button>
                                
                                <!-- Modal -->
                                <div class="modal fade" id="detalleModal<?= $registro['id'] ?>" tabindex="-1">
                                    <div class="modal-dialog modal-lg">
                                        <div class="modal-content">
                                            <div class="modal-header">
                                                <h5 class="modal-title">
                                                    Detalle de Auditoría #<?= $registro['id'] ?>
                                                </h5>
                                                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                            </div>
                                            <div class="modal-body">
                                                <?php if ($registro['valores_anteriores']): ?>
                                                <h6 class="text-muted">Valores Anteriores:</h6>
                                                <pre class="bg-light p-3 rounded"><?= json_encode(json_decode($registro['valores_anteriores']), JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE) ?></pre>
                                                <?php endif; ?>
                                                
                                                <?php if ($registro['valores_nuevos']): ?>
                                                <h6 class="text-muted mt-3">Valores Nuevos:</h6>
                                                <pre class="bg-light p-3 rounded"><?= json_encode(json_decode($registro['valores_nuevos']), JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE) ?></pre>
                                                <?php endif; ?>
                                            </div>
                                            <div class="modal-footer">
                                                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cerrar</button>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <?php else: ?>
                                <small class="text-muted">Sin detalles</small>
                                <?php endif; ?>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<?php require_once __DIR__ . '/../layouts/footer.php'; ?>
