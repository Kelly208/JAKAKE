            <!-- Sidebar -->
            <nav class="col-md-2 d-none d-md-block bg-light sidebar">
                <div class="position-sticky pt-3">
                    <ul class="nav flex-column">
                        <li class="nav-item">
                            <a class="nav-link <?= $_SERVER['REQUEST_URI'] === '/dashboard' ? 'active' : '' ?>" href="/dashboard">
                                <i class="bi bi-speedometer2"></i> Dashboard
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link <?= strpos($_SERVER['REQUEST_URI'], '/ventas') === 0 ? 'active' : '' ?>" href="/ventas">
                                <i class="bi bi-cart-plus"></i> Ventas
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link <?= strpos($_SERVER['REQUEST_URI'], '/productos') === 0 ? 'active' : '' ?>" href="/productos">
                                <i class="bi bi-box-seam"></i> Productos
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link <?= strpos($_SERVER['REQUEST_URI'], '/clientes') === 0 ? 'active' : '' ?>" href="/clientes">
                                <i class="bi bi-people"></i> Clientes
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link <?= strpos($_SERVER['REQUEST_URI'], '/devoluciones') === 0 ? 'active' : '' ?>" href="/devoluciones">
                                <i class="bi bi-arrow-return-left"></i> Devoluciones
                            </a>
                        </li>
                        
                        <?php if ($isAdmin): ?>
                        <li class="nav-item mt-3">
                            <h6 class="sidebar-heading px-3 mt-4 mb-1 text-muted">
                                <span>Administración</span>
                            </h6>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link <?= strpos($_SERVER['REQUEST_URI'], '/usuarios') === 0 ? 'active' : '' ?>" href="/usuarios">
                                <i class="bi bi-person-badge"></i> Usuarios
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link <?= strpos($_SERVER['REQUEST_URI'], '/proveedores') === 0 ? 'active' : '' ?>" href="/proveedores">
                                <i class="bi bi-truck"></i> Proveedores
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link <?= strpos($_SERVER['REQUEST_URI'], '/reportes') === 0 ? 'active' : '' ?>" href="/reportes">
                                <i class="bi bi-graph-up"></i> Reportes
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link <?= strpos($_SERVER['REQUEST_URI'], '/usuarios') === 0 ? 'active' : '' ?>" href="/usuarios">
                                <i class="bi bi-people-fill"></i> Usuarios
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link <?= strpos($_SERVER['REQUEST_URI'], '/auditoria') === 0 ? 'active' : '' ?>" href="/auditoria">
                                <i class="bi bi-clipboard-data"></i> Auditoría
                            </a>
                        </li>
                        <?php else: ?>
                        <li class="nav-item mt-3">
                            <a class="nav-link <?= strpos($_SERVER['REQUEST_URI'], '/reportes') === 0 ? 'active' : '' ?>" href="/reportes">
                                <i class="bi bi-graph-up"></i> Reportes
                            </a>
                        </li>
                        <?php endif; ?>
                    </ul>
                </div>
            </nav>

            <!-- Contenido principal -->
            <main class="col-md-10 ms-sm-auto px-md-4">
                <div class="pt-3 pb-2 mb-3">
