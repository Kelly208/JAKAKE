<?php require_once __DIR__ . '/../layouts/header.php'; ?>
<?php require_once __DIR__ . '/../layouts/sidebar.php'; ?>

<nav aria-label="breadcrumb">
    <ol class="breadcrumb">
        <li class="breadcrumb-item"><a href="/dashboard">Dashboard</a></li>
        <li class="breadcrumb-item"><a href="/ventas">Ventas</a></li>
        <li class="breadcrumb-item active">Nueva Venta</li>
    </ol>
</nav>

<h1 class="h2 mb-4">
    <i class="bi bi-cart-plus"></i> Nueva Venta
</h1>

<form method="POST" action="/ventas/nueva" id="formVenta">
    <input type="hidden" name="csrf_token" value="<?= \App\Utils\Security::generateToken() ?>">
    <input type="hidden" name="productos" id="productosJSON">

    <div class="row">
        <div class="col-lg-8">
            <!-- Selección de Cliente -->
            <div class="card shadow mb-4">
                <div class="card-header">
                    <h6 class="m-0 font-weight-bold text-primary">
                        <i class="bi bi-person"></i> Información del Cliente
                    </h6>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-8 mb-3">
                            <label for="cliente_id" class="form-label">Cliente <span class="text-danger">*</span></label>
                            <select class="form-select" id="cliente_id" name="cliente_id" required>
                                <option value="">Seleccione un cliente...</option>
                                <?php foreach ($clientes as $cliente): ?>
                                <option value="<?= $cliente['id'] ?>">
                                    <?= htmlspecialchars($cliente['nombre']) ?> 
                                    - <?= htmlspecialchars($cliente['cedula']) ?>
                                </option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div class="col-md-4 mb-3">
                            <label for="medio_pago" class="form-label">Medio de Pago <span class="text-danger">*</span></label>
                            <select class="form-select" id="medio_pago" name="medio_pago" required>
                                <option value="efectivo">Efectivo</option>
                                <option value="tarjeta">Tarjeta</option>
                                <option value="transferencia">Transferencia</option>
                            </select>
                        </div>
                    </div>
                    
                    <!-- Sección de Bono Regalo -->
                    <div class="row">
                        <div class="col-md-12">
                            <div class="card bg-light border">
                                <div class="card-body">
                                    <h6 class="mb-3"><i class="bi bi-gift"></i> ¿Tiene un bono regalo?</h6>
                                    <div class="row">
                                        <div class="col-md-8">
                                            <input type="text" class="form-control" id="codigo_bono" name="codigo_bono" 
                                                   placeholder="Ingrese el código del bono (ej: BONO-000001)">
                                            <input type="hidden" id="bono_id" name="bono_id">
                                        </div>
                                        <div class="col-md-4">
                                            <button type="button" class="btn btn-outline-success w-100" id="btnValidarBono">
                                                <i class="bi bi-check-circle"></i> Validar Bono
                                            </button>
                                        </div>
                                    </div>
                                    <div id="bonoInfo" class="mt-3" style="display: none;">
                                        <div class="alert alert-success mb-0">
                                            <strong><i class="bi bi-check-circle-fill"></i> Bono válido</strong><br>
                                            <span id="bonoDetalle"></span>
                                            <button type="button" class="btn btn-sm btn-outline-danger float-end" id="btnQuitarBono">
                                                <i class="bi bi-x"></i> Quitar
                                            </button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Selección de Productos -->
            <div class="card shadow mb-4">
                <div class="card-header">
                    <h6 class="m-0 font-weight-bold text-primary">
                        <i class="bi bi-box-seam"></i> Agregar Productos
                    </h6>
                </div>
                <div class="card-body">
                    <div class="row mb-3">
                        <div class="col-md-6">
                            <label for="producto_select" class="form-label">Producto</label>
                            <select class="form-select" id="producto_select">
                                <option value="">Seleccione un producto...</option>
                                <?php foreach ($productos as $producto): ?>
                                <option value="<?= $producto['id'] ?>" 
                                        data-nombre="<?= htmlspecialchars($producto['nombre']) ?>"
                                        data-precio="<?= $producto['precio'] ?>"
                                        data-stock="<?= $producto['cantidad'] ?>"
                                        data-codigo="<?= htmlspecialchars($producto['codigo']) ?>">
                                    <?= htmlspecialchars($producto['nombre']) ?> 
                                    - $<?= number_format($producto['precio'], 0, ',', '.') ?>
                                    (Stock: <?= $producto['cantidad'] ?>)
                                </option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div class="col-md-3">
                            <label for="cantidad_producto" class="form-label">Cantidad</label>
                            <input type="number" class="form-control" id="cantidad_producto" min="1" value="1">
                        </div>
                        <div class="col-md-3">
                            <label class="form-label">&nbsp;</label>
                            <button type="button" class="btn btn-success w-100" id="btnAgregarProducto">
                                <i class="bi bi-plus-circle"></i> Agregar
                            </button>
                        </div>
                    </div>

                    <div class="table-responsive">
                        <table class="table table-hover" id="tablaCarrito">
                            <thead class="table-light">
                                <tr>
                                    <th>Código</th>
                                    <th>Producto</th>
                                    <th>Precio Unit.</th>
                                    <th>Cantidad</th>
                                    <th>Subtotal</th>
                                    <th>Acciones</th>
                                </tr>
                            </thead>
                            <tbody id="carritoBody">
                                <tr id="carritoVacio">
                                    <td colspan="6" class="text-center text-muted">
                                        <i class="bi bi-cart-x"></i> No hay productos agregados
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-lg-4">
            <!-- Resumen de Venta -->
            <div class="card shadow mb-4 sticky-top" style="top: 20px;">
                <div class="card-header bg-primary text-white">
                    <h6 class="m-0 font-weight-bold">
                        <i class="bi bi-calculator"></i> Resumen de Venta
                    </h6>
                </div>
                <div class="card-body">
                    <div class="d-flex justify-content-between mb-2">
                        <span>Subtotal:</span>
                        <span id="subtotalVenta" class="currency">$0</span>
                    </div>
                    <div class="d-flex justify-content-between mb-2">
                        <span>IVA (19%):</span>
                        <span id="ivaVenta" class="currency">$0</span>
                    </div>
                    <div class="d-flex justify-content-between mb-2" id="descuentoBonoRow" style="display: none;">
                        <span class="text-success"><i class="bi bi-gift"></i> Descuento Bono:</span>
                        <span id="descuentoBono" class="currency text-success">-$0</span>
                    </div>
                    <hr>
                    <div class="d-flex justify-content-between mb-3">
                        <strong>Total:</strong>
                        <strong id="totalVenta" class="currency h4 text-primary">$0</strong>
                    </div>

                    <div class="alert alert-info small">
                        <i class="bi bi-info-circle"></i> 
                        <strong>Productos:</strong> <span id="cantidadProductos">0</span>
                    </div>

                    <div class="d-grid gap-2">
                        <button type="submit" class="btn btn-primary btn-lg" id="btnProcesarVenta" disabled>
                            <i class="bi bi-check-circle"></i> Procesar Venta
                        </button>
                        <a href="/ventas" class="btn btn-secondary">
                            <i class="bi bi-x-circle"></i> Cancelar
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</form>

<script>
// Carrito de compras
let carrito = [];
let bonoAplicado = null;

document.addEventListener('DOMContentLoaded', function() {
    const btnAgregar = document.getElementById('btnAgregarProducto');
    const productoSelect = document.getElementById('producto_select');
    const cantidadInput = document.getElementById('cantidad_producto');
    const formVenta = document.getElementById('formVenta');
    const btnValidarBono = document.getElementById('btnValidarBono');
    const btnQuitarBono = document.getElementById('btnQuitarBono');
    const clienteSelect = document.getElementById('cliente_id');

    // Validar bono
    btnValidarBono.addEventListener('click', async function() {
        const codigoBono = document.getElementById('codigo_bono').value.trim();
        const clienteId = clienteSelect.value;

        if (!clienteId) {
            jakake.showToast('Primero seleccione un cliente', 'warning');
            return;
        }

        if (!codigoBono) {
            jakake.showToast('Ingrese el código del bono', 'warning');
            return;
        }

        try {
            const response = await fetch(`/ventas/validar-bono?codigo=${encodeURIComponent(codigoBono)}&cliente_id=${clienteId}`, {
                method: 'GET',
                credentials: 'same-origin',
                headers: {
                    'Accept': 'application/json'
                }
            });
            
            if (!response.ok) {
                throw new Error(`HTTP error! status: ${response.status}`);
            }
            
            const data = await response.json();

            if (data.success) {
                bonoAplicado = data.bono;
                document.getElementById('bono_id').value = data.bono.id;
                document.getElementById('bonoDetalle').textContent = `Valor: $${jakake.formatCurrency(data.bono.valor)} - Válido hasta: ${data.bono.fecha_vencimiento}`;
                document.getElementById('bonoInfo').style.display = 'block';
                document.getElementById('codigo_bono').disabled = true;
                btnValidarBono.disabled = true;
                actualizarTotales();
                jakake.showToast('Bono aplicado correctamente', 'success');
            } else {
                jakake.showToast(data.error || 'Bono inválido', 'danger');
            }
        } catch (error) {
            console.error('Error al validar bono:', error);
            jakake.showToast('Error de conexión al validar el bono', 'danger');
        }
    });

    // Quitar bono
    btnQuitarBono.addEventListener('click', function() {
        bonoAplicado = null;
        document.getElementById('bono_id').value = '';
        document.getElementById('codigo_bono').value = '';
        document.getElementById('codigo_bono').disabled = false;
        document.getElementById('bonoInfo').style.display = 'none';
        btnValidarBono.disabled = false;
        actualizarTotales();
        jakake.showToast('Bono removido', 'info');
    });


    btnAgregar.addEventListener('click', function() {
        const productoId = productoSelect.value;
        if (!productoId) {
            jakake.showToast('Seleccione un producto', 'warning');
            return;
        }

        const option = productoSelect.options[productoSelect.selectedIndex];
        const nombre = option.dataset.nombre;
        const precio = parseFloat(option.dataset.precio);
        const stock = parseInt(option.dataset.stock);
        const codigo = option.dataset.codigo;
        const cantidad = parseInt(cantidadInput.value);

        if (cantidad <= 0) {
            jakake.showToast('La cantidad debe ser mayor a 0', 'warning');
            return;
        }

        if (cantidad > stock) {
            jakake.showToast('No hay suficiente stock disponible', 'danger');
            return;
        }

        // Verificar si ya existe en el carrito
        const existente = carrito.find(item => item.producto_id === productoId);
        if (existente) {
            const nuevaCantidad = existente.cantidad + cantidad;
            if (nuevaCantidad > stock) {
                jakake.showToast('Excede el stock disponible', 'danger');
                return;
            }
            existente.cantidad = nuevaCantidad;
        } else {
            carrito.push({
                producto_id: productoId,
                nombre: nombre,
                codigo: codigo,
                precio: precio,
                cantidad: cantidad,
                stock: stock
            });
        }

        actualizarCarrito();
        productoSelect.value = '';
        cantidadInput.value = 1;
    });

    formVenta.addEventListener('submit', function(e) {
        if (carrito.length === 0) {
            e.preventDefault();
            jakake.showToast('Debe agregar al menos un producto', 'danger');
            return false;
        }

        // Preparar JSON de productos
        const productosData = carrito.map(item => ({
            producto_id: item.producto_id,
            cantidad: item.cantidad,
            precio: item.precio
        }));

        document.getElementById('productosJSON').value = JSON.stringify(productosData);
    });
});

function actualizarCarrito() {
    const tbody = document.getElementById('carritoBody');
    const carritoVacio = document.getElementById('carritoVacio');

    if (carrito.length === 0) {
        carritoVacio.style.display = 'table-row';
        document.getElementById('btnProcesarVenta').disabled = true;
        actualizarTotales();
        return;
    }

    carritoVacio.style.display = 'none';
    tbody.innerHTML = '';

    carrito.forEach((item, index) => {
        const subtotal = item.precio * item.cantidad;
        const row = `
            <tr>
                <td><code>${item.codigo}</code></td>
                <td>${item.nombre}</td>
                <td class="currency">$${jakake.formatCurrency(item.precio)}</td>
                <td>
                    <input type="number" class="form-control form-control-sm" 
                           value="${item.cantidad}" min="1" max="${item.stock}"
                           onchange="actualizarCantidad(${index}, this.value)">
                </td>
                <td class="currency">$${jakake.formatCurrency(subtotal)}</td>
                <td>
                    <button type="button" class="btn btn-sm btn-danger" onclick="eliminarProducto(${index})">
                        <i class="bi bi-trash"></i>
                    </button>
                </td>
            </tr>
        `;
        tbody.innerHTML += row;
    });

    document.getElementById('btnProcesarVenta').disabled = false;
    actualizarTotales();
}

function actualizarCantidad(index, nuevaCantidad) {
    nuevaCantidad = parseInt(nuevaCantidad);
    const item = carrito[index];

    if (nuevaCantidad <= 0) {
        jakake.showToast('La cantidad debe ser mayor a 0', 'warning');
        actualizarCarrito();
        return;
    }

    if (nuevaCantidad > item.stock) {
        jakake.showToast('Excede el stock disponible', 'danger');
        actualizarCarrito();
        return;
    }

    item.cantidad = nuevaCantidad;
    actualizarCarrito();
}

function eliminarProducto(index) {
    carrito.splice(index, 1);
    actualizarCarrito();
    jakake.showToast('Producto eliminado', 'info');
}

function actualizarTotales() {
    const subtotal = carrito.reduce((sum, item) => sum + (item.precio * item.cantidad), 0);
    const iva = subtotal * 0.19;
    let total = subtotal + iva;
    const cantidadTotal = carrito.reduce((sum, item) => sum + item.cantidad, 0);

    // Aplicar descuento del bono
    let descuentoBono = 0;
    if (bonoAplicado) {
        descuentoBono = Math.min(parseFloat(bonoAplicado.valor), total);
        total -= descuentoBono;
        document.getElementById('descuentoBonoRow').style.display = 'flex';
        document.getElementById('descuentoBono').textContent = '-$' + jakake.formatCurrency(descuentoBono);
    } else {
        document.getElementById('descuentoBonoRow').style.display = 'none';
    }

    document.getElementById('subtotalVenta').textContent = '$' + jakake.formatCurrency(subtotal);
    document.getElementById('ivaVenta').textContent = '$' + jakake.formatCurrency(iva);
    document.getElementById('totalVenta').textContent = '$' + jakake.formatCurrency(total);
    document.getElementById('cantidadProductos').textContent = cantidadTotal;
}
</script>

<?php require_once __DIR__ . '/../layouts/footer.php'; ?>
