<?php
$title = 'Iniciar Sesión - Papelería JAKAKE';
ob_start();
?>

<div class="container mt-5">
    <div class="row justify-content-center">
        <div class="col-md-4">
            <div class="card shadow">
                <div class="card-header bg-primary text-white text-center">
                    <h3>🔐 Papelería JAKAKE</h3>
                    <p class="mb-0">Iniciar Sesión</p>
                </div>
                <div class="card-body">
                    <?php if (isset($error)): ?>
                    <div class="alert alert-danger alert-dismissible fade show" role="alert">
                        <?= htmlspecialchars($error) ?>
                        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                    </div>
                    <?php endif; ?>
                    
                    <form method="POST" action="/login">
                        <input type="hidden" name="csrf_token" value="<?= \App\Utils\Security::generateToken() ?>">
                        
                        <div class="mb-3">
                            <label for="email" class="form-label">Email</label>
                            <input type="email" class="form-control" id="email" name="email" 
                                   placeholder="usuario@jakake.com" required autofocus>
                        </div>
                        
                        <div class="mb-3">
                            <label for="password" class="form-label">Contraseña</label>
                            <input type="password" class="form-control" id="password" name="password" 
                                   placeholder="••••••••" required>
                        </div>
                        
                        <button type="submit" class="btn btn-primary w-100">
                            Ingresar
                        </button>
                    </form>
                    
                    <hr>
                    <div class="text-center">
                        <small class="text-muted">
                            Al iniciar sesión aceptas nuestras 
                            <a href="#" class="text-decoration-none">políticas de datos</a>
                        </small>
                    </div>
                </div>
                <div class="card-footer text-center text-muted">
                    <small>Papelería JAKAKE &copy; 2025</small>
                </div>
            </div>
            
            <!-- Usuarios de prueba (solo para desarrollo) -->
            <div class="card mt-3 bg-light">
                <div class="card-body">
                    <h6 class="card-title">👤 Usuarios de prueba:</h6>
                    <small>
                        <strong>Admin:</strong> admin@jakake.com / admin123<br>
                        <strong>Cajero:</strong> cajero@jakake.com / cajero123
                    </small>
                </div>
            </div>
        </div>
    </div>
</div>

<?php $content = ob_get_clean(); ?>
<?php require __DIR__ . '/../layouts/main.php'; ?>
