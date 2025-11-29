<?php
use App\Utils\Session;
Session::start();

// Verificar autenticación
if (!Session::get('user_id')) {
    header('Location: /login');
    exit;
}

$userName = Session::get('user_name');
$userRol = Session::get('user_rol');
$isAdmin = $userRol === 'administrador';
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= $title ?? 'Papelería JAKAKE' ?></title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css">
    <link rel="stylesheet" href="/css/style.css">
</head>
<body>
    <!-- Barra superior simplificada -->
    <nav class="navbar navbar-dark bg-primary py-2">
        <div class="container-fluid">
            <a class="navbar-brand fw-bold" href="/dashboard">
                <i class="bi bi-shop"></i> Papelería JAKAKE
            </a>
            <div class="d-flex align-items-center">
                <span class="text-white me-3">
                    <i class="bi bi-person-circle"></i> <?= htmlspecialchars($userName) ?>
                    <span class="badge bg-light text-primary ms-1"><?= $isAdmin ? 'Admin' : 'Cajero' ?></span>
                </span>
                <a href="/logout" class="btn btn-sm btn-outline-light" title="Cerrar Sesión">
                    <i class="bi bi-box-arrow-right"></i>
                </a>
            </div>
        </div>
    </nav>

    <!-- Contenedor principal -->
    <div class="container-fluid">
        <div class="row">
