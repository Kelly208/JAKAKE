<?php
use App\Utils\Session;

Session::start();
$isLoggedIn = Session::get('user_id') !== null;
$userName = Session::get('user_name');
$userRole = Session::get('user_role');
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Papelería Jakake - <?= $title ?? 'Sistema' ?></title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css" rel="stylesheet">
</head>
<body>
    <?php if ($isLoggedIn): ?>
    <nav class="navbar navbar-expand-lg navbar-dark bg-primary">
        <div class="container-fluid">
            <a class="navbar-brand" href="/">Jakake Sistema</a>
            <div class="navbar-nav ms-auto">
                <span class="navbar-text me-3">
                    <?= htmlspecialchars($userName) ?> (<?= htmlspecialchars($userRole) ?>)
                </span>
                <a class="nav-link" href="/logout">Cerrar Sesión</a>
            </div>
        </div>
    </nav>
    
    <div class="container-fluid">
        <div class="row">
            <nav class="col-md-2 bg-light p-3">
                <ul class="nav flex-column">
                    <li class="nav-item">
                        <a class="nav-link" href="/dashboard">Dashboard</a>
                    </li>
                    <?php if ($userRole === 'Administrador'): ?>
                    <li class="nav-item">
                        <a class="nav-link" href="/productos">Productos</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="/proveedores">Proveedores</a>
                    </li>
                    <?php endif; ?>
                    <li class="nav-item">
                        <a class="nav-link" href="/ventas/registrar">Nueva Venta</a>
                    </li>
                    <?php if ($userRole === 'Administrador'): ?>
                    <li class="nav-item">
                        <a class="nav-link" href="/reportes">Reportes</a>
                    </li>
                    <?php endif; ?>
                </ul>
            </nav>
            
            <main class="col-md-10 p-4">
                <?= $content ?>
            </main>
        </div>
    </div>
    <?php else: ?>
    <main>
        <?= $content ?>
    </main>
    <?php endif; ?>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
</body>
</html>