<?php require_once __DIR__ . '/../layouts/header.php'; ?>
<?php require_once __DIR__ . '/../layouts/sidebar.php'; ?>

<nav aria-label="breadcrumb">
    <ol class="breadcrumb">
        <li class="breadcrumb-item"><a href="/dashboard">Dashboard</a></li>
        <li class="breadcrumb-item"><a href="/clientes">Clientes</a></li>
        <li class="breadcrumb-item active">Nuevo Cliente</li>
    </ol>
</nav>

<h1 class="h2 mb-4">
    <i class="bi bi-plus-circle"></i> Nuevo Cliente
</h1>

<div class="row">
    <div class="col-lg-8">
        <div class="card shadow">
            <div class="card-header">
                <h6 class="m-0 font-weight-bold text-primary">
                    <i class="bi bi-person"></i> Información del Cliente
                </h6>
            </div>
            <div class="card-body">
                <form method="POST" action="/clientes/crear" id="formCliente">
                    <input type="hidden" name="csrf_token" value="<?= \App\Utils\Security::generateToken() ?>">
                    
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="cedula" class="form-label">Cédula <span class="text-danger">*</span></label>
                            <input type="text" class="form-control" id="cedula" name="cedula" required>
                            <small class="text-muted">Número de identificación único</small>
                        </div>

                        <div class="col-md-6 mb-3">
                            <label for="email" class="form-label">Email <span class="text-danger">*</span></label>
                            <input type="email" class="form-control" id="email" name="email" required>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-12 mb-3">
                            <label for="nombre" class="form-label">Nombre Completo <span class="text-danger">*</span></label>
                            <input type="text" class="form-control" id="nombre" name="nombre" required>
                            <small class="text-muted">Ingrese el nombre completo del cliente</small>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="telefono" class="form-label">Teléfono</label>
                            <input type="tel" class="form-control" id="telefono" name="telefono">
                        </div>

                        <div class="col-md-6 mb-3">
                            <label for="direccion" class="form-label">Dirección</label>
                            <input type="text" class="form-control" id="direccion" name="direccion">
                        </div>
                    </div>

                    <hr class="my-4">

                    <div class="card bg-light">
                        <div class="card-body">
                            <h6 class="card-title">
                                <i class="bi bi-shield-check"></i> Políticas de Datos
                            </h6>
                            <p class="card-text text-muted small">
                                Al registrarse, el cliente acepta que Papelería JAKAKE almacene y procese sus datos personales 
                                con el fin de brindar un mejor servicio. Los datos serán protegidos conforme a la 
                                Ley 1581 de 2012 y no serán compartidos con terceros.
                            </p>
                            <div class="form-check">
                                <input class="form-check-input" type="checkbox" id="acepta_politicas" name="acepta_politicas" required>
                                <label class="form-check-label" for="acepta_politicas">
                                    El cliente acepta las <strong>políticas de tratamiento de datos personales</strong>
                                    <span class="text-danger">*</span>
                                </label>
                            </div>
                        </div>
                    </div>

                    <hr class="my-4">

                    <div class="d-flex justify-content-between">
                        <a href="/clientes" class="btn btn-secondary">
                            <i class="bi bi-arrow-left"></i> Cancelar
                        </a>
                        <button type="submit" class="btn btn-primary">
                            <i class="bi bi-save"></i> Guardar Cliente
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
                    <i class="bi bi-lightbulb text-warning"></i> La cédula debe ser única y no se podrá modificar después.
                </p>
                <hr>
                <p class="text-muted small mb-0">
                    <i class="bi bi-shield-check text-info"></i> Es obligatorio aceptar las políticas de datos para registrar al cliente.
                </p>
            </div>
        </div>

        <div class="card shadow">
            <div class="card-header">
                <h6 class="m-0 font-weight-bold text-primary">
                    <i class="bi bi-file-earmark-text"></i> Cumplimiento Legal
                </h6>
            </div>
            <div class="card-body">
                <p class="text-muted small mb-0">
                    Este sistema cumple con la <strong>Ley 1581 de 2012</strong> (Protección de Datos Personales) 
                    y registra la fecha, versión y dirección IP de la aceptación de políticas.
                </p>
            </div>
        </div>
    </div>
</div>

<?php require_once __DIR__ . '/../layouts/footer.php'; ?>
