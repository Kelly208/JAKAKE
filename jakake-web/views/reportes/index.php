<?php require_once __DIR__ . '/../layouts/header.php'; ?>
<?php require_once __DIR__ . '/../layouts/sidebar.php'; ?>

<nav aria-label="breadcrumb">
    <ol class="breadcrumb">
        <li class="breadcrumb-item"><a href="/dashboard">Dashboard</a></li>
        <li class="breadcrumb-item active">Reportes</li>
    </ol>
</nav>

<h1 class="h2 mb-4">
    <i class="bi bi-bar-chart-line"></i> Reportes y Análisis
</h1>

<div class="row">
    <!-- Reporte de Ventas -->
    <div class="col-lg-4 mb-4">
        <div class="card shadow h-100 hover-card">
            <div class="card-body text-center">
                <div class="mb-3">
                    <i class="bi bi-cart-check text-primary" style="font-size: 4rem;"></i>
                </div>
                <h5 class="card-title">Reporte de Ventas</h5>
                <p class="card-text text-muted">
                    Análisis detallado de ventas por período, medios de pago y clientes frecuentes.
                </p>
                <ul class="list-unstyled text-start small text-muted">
                    <li><i class="bi bi-check-circle text-success"></i> Ventas por período</li>
                    <li><i class="bi bi-check-circle text-success"></i> Análisis por día</li>
                    <li><i class="bi bi-check-circle text-success"></i> Medios de pago</li>
                    <li><i class="bi bi-check-circle text-success"></i> Top clientes</li>
                </ul>
                <a href="/reportes/ventas" class="btn btn-primary w-100">
                    <i class="bi bi-graph-up"></i> Ver Reporte
                </a>
            </div>
        </div>
    </div>

    <!-- Reporte de Productos -->
    <div class="col-lg-4 mb-4">
        <div class="card shadow h-100 hover-card">
            <div class="card-body text-center">
                <div class="mb-3">
                    <i class="bi bi-box-seam text-success" style="font-size: 4rem;"></i>
                </div>
                <h5 class="card-title">Reporte de Productos</h5>
                <p class="card-text text-muted">
                    Estado del inventario, productos más vendidos y alertas de stock.
                </p>
                <ul class="list-unstyled text-start small text-muted">
                    <li><i class="bi bi-check-circle text-success"></i> Productos más vendidos</li>
                    <li><i class="bi bi-check-circle text-success"></i> Alertas de stock</li>
                    <li><i class="bi bi-check-circle text-success"></i> Análisis por tipo</li>
                    <li><i class="bi bi-check-circle text-success"></i> Valor de inventario</li>
                </ul>
                <a href="/reportes/productos" class="btn btn-success w-100">
                    <i class="bi bi-boxes"></i> Ver Reporte
                </a>
            </div>
        </div>
    </div>

    <!-- Reporte de Clientes -->
    <div class="col-lg-4 mb-4">
        <div class="card shadow h-100 hover-card">
            <div class="card-body text-center">
                <div class="mb-3">
                    <i class="bi bi-people text-info" style="font-size: 4rem;"></i>
                </div>
                <h5 class="card-title">Reporte de Clientes</h5>
                <p class="card-text text-muted">
                    Análisis de clientes, comportamiento de compra y segmentación.
                </p>
                <ul class="list-unstyled text-start small text-muted">
                    <li><i class="bi bi-check-circle text-success"></i> Clientes frecuentes</li>
                    <li><i class="bi bi-check-circle text-success"></i> Clientes nuevos</li>
                    <li><i class="bi bi-check-circle text-success"></i> Clientes inactivos</li>
                    <li><i class="bi bi-check-circle text-success"></i> Estadísticas generales</li>
                </ul>
                <a href="/reportes/clientes" class="btn btn-info w-100">
                    <i class="bi bi-person-lines-fill"></i> Ver Reporte
                </a>
            </div>
        </div>
    </div>
</div>

<div class="row">
    <div class="col-12">
        <div class="card shadow">
            <div class="card-header bg-primary text-white">
                <h6 class="m-0 font-weight-bold">
                    <i class="bi bi-info-circle"></i> Información sobre Reportes
                </h6>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-4">
                        <h6 class="text-primary"><i class="bi bi-calendar-range"></i> Filtros por Fecha</h6>
                        <p class="small text-muted">
                            Todos los reportes permiten seleccionar rangos de fechas personalizados para análisis específicos.
                        </p>
                    </div>
                    <div class="col-md-4">
                        <h6 class="text-success"><i class="bi bi-download"></i> Exportación</h6>
                        <p class="small text-muted">
                            Los reportes pueden ser impresos o exportados para su análisis externo y presentaciones.
                        </p>
                    </div>
                    <div class="col-md-4">
                        <h6 class="text-info"><i class="bi bi-graph-up-arrow"></i> Datos en Tiempo Real</h6>
                        <p class="small text-muted">
                            Toda la información se obtiene directamente de la base de datos con los datos más actualizados.
                        </p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
.hover-card {
    transition: transform 0.3s ease, box-shadow 0.3s ease;
}
.hover-card:hover {
    transform: translateY(-5px);
    box-shadow: 0 1rem 3rem rgba(0, 0, 0, 0.175) !important;
}
</style>

<?php require_once __DIR__ . '/../layouts/footer.php'; ?>
