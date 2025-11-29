<?php require_once __DIR__ . '/../layouts/header.php'; ?>
<?php require_once __DIR__ . '/../layouts/sidebar.php'; ?>

<nav aria-label="breadcrumb">
    <ol class="breadcrumb">
        <li class="breadcrumb-item"><a href="/dashboard">Dashboard</a></li>
        <li class="breadcrumb-item"><a href="/devoluciones">Devoluciones</a></li>
        <li class="breadcrumb-item active">Nueva Devolución</li>
    </ol>
</nav>

<h1 class="h2 mb-4">
    <i class="bi bi-arrow-counterclockwise"></i> Nueva Devolución
</h1>

<form method="POST" action="/devoluciones/nueva" id="formDevolucion">
    <input type="hidden" name="csrf_token" value="<?= \App\Utils\Security::generateToken() ?>">
    <input type="hidden" name="productos" id="productosJSON">
    <input type="hidden" name="venta_id" id="venta_id_hidden" value="<?= $venta['id'] ?? '' ?>">

    <div class="row">
        <div class="col-lg-8">
            <!-- Buscar Venta -->
            <div class="card shadow mb-4">
                <div class="card-header">
                    <h6 class="m-0 font-weight-bold text-primary">
                        <i class="bi bi-search"></i> Buscar Venta
                    </h6>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-8 mb-3">
                            <label for="venta_id_input" class="form-label">ID de Venta <span class="text-danger">*</span></label>
                            <input type="number" class="form-control" id="venta_id_input" 
                                   placeholder="Ingrese el ID de la venta" 
                                   value="<?= $venta['id'] ?? '' ?>">
                        </div>
                        <div class="col-md-4 mb-3">
                            <label class="form-label">&nbsp;</label>
                            <button type="button" class="btn btn-primary w-100" id="btnBuscarVenta">
                                <i class="bi bi-search"></i> Buscar
                            </button>
                        </div>
                    </div>

                    <div id="infoVenta" style="display: <?= $venta ? 'block' : 'none' ?>;">
                        <?php if ($venta): ?>
                        <div class="alert alert-success">
                            <i class="bi bi-check-circle"></i> <strong>Venta encontrada</strong>
                        </div>
                        <div class="row">
                            <div class="col-md-6">
                                <p class="mb-1"><strong>Cliente:</strong> 
                                    <?= htmlspecialchars($venta['cliente_nombre'] ?? 'Cliente general') ?>
                                </p>
                                <p class="mb-0"><strong>Fecha:</strong> <?= date('d/m/Y H:i', strtotime($venta['fecha'])) ?></p>
                            </div>
                            <div class="col-md-6">
                                <p class="mb-1"><strong>Total:</strong> $<?= number_format($venta['total'], 0, ',', '.') ?></p>
                                <p class="mb-0"><strong>Estado:</strong> 
                                    <span class="badge bg-success"><?= ucfirst($venta['estado']) ?></span>
                                </p>
                            </div>
                        </div>
                        <?php endif; ?>
                    </div>
                </div>
            </div>

            <!-- Productos a Devolver -->
            <div class="card shadow mb-4" id="cardProductos" style="display: <?= !empty($detalles) ? 'block' : 'none' ?>;">
                <div class="card-header">
                    <h6 class="m-0 font-weight-bold text-primary">
                        <i class="bi bi-box-seam"></i> Productos a Devolver
                    </h6>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-hover" id="tablaProductos">
                            <thead class="table-light">
                                <tr>
                                    <th width="50">
                                        <input type="checkbox" id="selectAll" title="Seleccionar todos">
                                    </th>
                                    <th>Código</th>
                                    <th>Producto</th>
                                    <th>Precio Unit.</th>
                                    <th>Cant. Vendida</th>
                                    <th>Cant. a Devolver</th>
                                    <th>Subtotal</th>
                                </tr>
                            </thead>
                            <tbody id="productosBody">
                                <?php if (!empty($detalles)): ?>
                                    <?php foreach ($detalles as $detalle): ?>
                                    <tr data-producto-id="<?= $detalle['producto_id'] ?>" 
                                        data-precio="<?= $detalle['precio_unitario'] ?>"
                                        data-max="<?= $detalle['cantidad'] ?>">
                                        <td>
                                            <input type="checkbox" class="producto-checkbox" 
                                                   value="<?= $detalle['producto_id'] ?>">
                                        </td>
                                        <td><code><?= htmlspecialchars($detalle['producto_codigo']) ?></code></td>
                                        <td><?= htmlspecialchars($detalle['producto_nombre']) ?></td>
                                        <td class="currency">$<?= number_format($detalle['precio_unitario'], 0, ',', '.') ?></td>
                                        <td><?= $detalle['cantidad'] ?></td>
                                        <td>
                                            <input type="number" class="form-control form-control-sm cantidad-devolver" 
                                                   min="1" max="<?= $detalle['cantidad'] ?>" value="<?= $detalle['cantidad'] ?>" 
                                                   data-producto-id="<?= $detalle['producto_id'] ?>" disabled>
                                        </td>
                                        <td class="subtotal-producto currency">$0</td>
                                    </tr>
                                    <?php endforeach; ?>
                                <?php endif; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

            <!-- Motivo de Devolución -->
            <div class="card shadow mb-4" id="cardMotivo" style="display: <?= !empty($detalles) ? 'block' : 'none' ?>;">
                <div class="card-header">
                    <h6 class="m-0 font-weight-bold text-primary">
                        <i class="bi bi-chat-left-text"></i> Motivo de Devolución
                    </h6>
                </div>
                <div class="card-body">
                    <textarea class="form-control" name="motivo" id="motivo" rows="4" 
                              placeholder="Describa el motivo de la devolución..." required></textarea>
                </div>
            </div>
        </div>

        <div class="col-lg-4">
            <!-- Resumen de Devolución -->
            <div class="card shadow mb-4 sticky-top" style="top: 20px;">
                <div class="card-header bg-warning text-dark">
                    <h6 class="m-0 font-weight-bold">
                        <i class="bi bi-calculator"></i> Resumen de Devolución
                    </h6>
                </div>
                <div class="card-body">
                    <div class="d-flex justify-content-between mb-2">
                        <span>Productos seleccionados:</span>
                        <span id="cantidadProductos">0</span>
                    </div>
                    <div class="d-flex justify-content-between mb-2">
                        <span>Unidades a devolver:</span>
                        <span id="unidadesDevolver">0</span>
                    </div>
                    <hr>
                    <div class="d-flex justify-content-between mb-3">
                        <strong>Monto a devolver:</strong>
                        <strong id="montoDevolucion" class="currency h4 text-warning">$0</strong>
                    </div>

                    <div class="alert alert-info small">
                        <i class="bi bi-gift"></i> 
                        Se generará un <strong>bono regalo</strong> por el monto devuelto
                    </div>

                    <div class="d-grid gap-2">
                        <button type="submit" class="btn btn-warning btn-lg" id="btnProcesarDevolucion" disabled>
                            <i class="bi bi-check-circle"></i> Procesar Devolución
                        </button>
                        <a href="/devoluciones" class="btn btn-secondary">
                            <i class="bi bi-x-circle"></i> Cancelar
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</form>

<script>
let ventaCargada = <?= $venta ? 'true' : 'false' ?>;

document.addEventListener('DOMContentLoaded', function() {
    const btnBuscar = document.getElementById('btnBuscarVenta');
    const formDevolucion = document.getElementById('formDevolucion');
    const selectAll = document.getElementById('selectAll');

    if (ventaCargada) {
        actualizarResumen();
    }

    btnBuscar.addEventListener('click', function() {
        const ventaId = document.getElementById('venta_id_input').value;
        if (!ventaId) {
            jakake.showToast('Ingrese un ID de venta', 'warning');
            return;
        }

        fetch('/devoluciones/buscar-venta?venta_id=' + ventaId)
            .then(response => response.json())
            .then(data => {
                if (data.error) {
                    jakake.showToast(data.error, 'danger');
                    document.getElementById('infoVenta').style.display = 'none';
                    document.getElementById('cardProductos').style.display = 'none';
                    document.getElementById('cardMotivo').style.display = 'none';
                    return;
                }

                // Mostrar información de la venta
                const infoHTML = `
                    <div class="alert alert-success">
                        <i class="bi bi-check-circle"></i> <strong>Venta encontrada</strong>
                    </div>
                    <div class="row">
                        <div class="col-md-6">
                            <p class="mb-1"><strong>Cliente:</strong> ${data.venta.cliente_nombre || 'Cliente general'}</p>
                            <p class="mb-0"><strong>Fecha:</strong> ${new Date(data.venta.fecha).toLocaleString('es-CO')}</p>
                        </div>
                        <div class="col-md-6">
                            <p class="mb-1"><strong>Total:</strong> $${jakake.formatCurrency(data.venta.total)}</p>
                            <p class="mb-0"><strong>Estado:</strong> <span class="badge bg-success">${data.venta.estado}</span></p>
                        </div>
                    </div>
                `;
                document.getElementById('infoVenta').innerHTML = infoHTML;
                document.getElementById('infoVenta').style.display = 'block';
                document.getElementById('venta_id_hidden').value = ventaId;

                // Cargar productos
                const tbody = document.getElementById('productosBody');
                tbody.innerHTML = '';
                data.productos.forEach(producto => {
                    const row = `
                        <tr data-producto-id="${producto.producto_id}" 
                            data-precio="${producto.precio_unitario}"
                            data-max="${producto.cantidad}">
                            <td>
                                <input type="checkbox" class="producto-checkbox" value="${producto.producto_id}">
                            </td>
                            <td><code>${producto.producto_codigo}</code></td>
                            <td>${producto.producto_nombre}</td>
                            <td class="currency">$${jakake.formatCurrency(producto.precio_unitario)}</td>
                            <td>${producto.cantidad}</td>
                            <td>
                                <input type="number" class="form-control form-control-sm cantidad-devolver" 
                                       min="1" max="${producto.cantidad}" value="${producto.cantidad}" 
                                       data-producto-id="${producto.producto_id}" disabled>
                            </td>
                            <td class="subtotal-producto currency">$0</td>
                        </tr>
                    `;
                    tbody.innerHTML += row;
                });

                document.getElementById('cardProductos').style.display = 'block';
                document.getElementById('cardMotivo').style.display = 'block';

                configurarEventos();
                ventaCargada = true;
            })
            .catch(error => {
                console.error('Error:', error);
                jakake.showToast('Error al buscar venta', 'danger');
            });
    });

    if (ventaCargada) {
        configurarEventos();
    }

    selectAll.addEventListener('change', function() {
        const checkboxes = document.querySelectorAll('.producto-checkbox');
        checkboxes.forEach(cb => {
            cb.checked = this.checked;
            const cantidadInput = document.querySelector(`.cantidad-devolver[data-producto-id="${cb.value}"]`);
            cantidadInput.disabled = !this.checked;
        });
        actualizarResumen();
    });

    formDevolucion.addEventListener('submit', function(e) {
        const productosSeleccionados = obtenerProductosSeleccionados();
        if (productosSeleccionados.length === 0) {
            e.preventDefault();
            jakake.showToast('Debe seleccionar al menos un producto', 'danger');
            return false;
        }

        document.getElementById('productosJSON').value = JSON.stringify(productosSeleccionados);
    });
});

function configurarEventos() {
    const checkboxes = document.querySelectorAll('.producto-checkbox');
    checkboxes.forEach(cb => {
        cb.addEventListener('change', function() {
            const cantidadInput = document.querySelector(`.cantidad-devolver[data-producto-id="${this.value}"]`);
            cantidadInput.disabled = !this.checked;
            actualizarResumen();
        });
    });

    const cantidadInputs = document.querySelectorAll('.cantidad-devolver');
    cantidadInputs.forEach(input => {
        input.addEventListener('input', actualizarResumen);
    });
}

function obtenerProductosSeleccionados() {
    const productos = [];
    const checkboxes = document.querySelectorAll('.producto-checkbox:checked');
    
    checkboxes.forEach(cb => {
        const row = cb.closest('tr');
        const productoId = row.dataset.productoId;
        const cantidadInput = row.querySelector('.cantidad-devolver');
        const cantidad = parseInt(cantidadInput.value);

        if (cantidad > 0) {
            productos.push({
                producto_id: productoId,
                cantidad: cantidad
            });
        }
    });

    return productos;
}

function actualizarResumen() {
    const checkboxes = document.querySelectorAll('.producto-checkbox:checked');
    let totalMonto = 0;
    let totalUnidades = 0;

    checkboxes.forEach(cb => {
        const row = cb.closest('tr');
        const precio = parseFloat(row.dataset.precio);
        const cantidadInput = row.querySelector('.cantidad-devolver');
        const cantidad = parseInt(cantidadInput.value) || 0;
        const subtotal = precio * cantidad;

        row.querySelector('.subtotal-producto').textContent = '$' + jakake.formatCurrency(subtotal);
        totalMonto += subtotal;
        totalUnidades += cantidad;
    });

    document.getElementById('cantidadProductos').textContent = checkboxes.length;
    document.getElementById('unidadesDevolver').textContent = totalUnidades;
    document.getElementById('montoDevolucion').textContent = '$' + jakake.formatCurrency(totalMonto);

    document.getElementById('btnProcesarDevolucion').disabled = checkboxes.length === 0;
}
</script>

<?php require_once __DIR__ . '/../layouts/footer.php'; ?>
