-- =====================================================
-- DATOS DE PRUEBA
-- Sistema de Base de Datos - Papelería JAKAKE
-- =====================================================
-- Descripción: Inserción de datos de ejemplo para probar
--              todas las funcionalidades del sistema
-- Autor: Persona 1
-- Fecha: 2025-11-27
-- =====================================================

USE papeleria_jakake;

-- =====================================================
-- 1. POLÍTICAS DE DATOS
-- =====================================================

INSERT INTO politicas_datos (version, titulo, contenido, fecha_vigencia, activa) VALUES
('1.0', 'Política de Protección de Datos Personales', 
'Papelería JAKAKE se compromete a proteger la privacidad y los datos personales de sus clientes y usuarios conforme a la Ley 1581 de 2012 de Colombia. Los datos recopilados serán utilizados únicamente para fines comerciales, facturación y comunicación con el cliente. El titular de los datos tiene derecho a conocer, actualizar, rectificar y suprimir su información personal en cualquier momento.',
'2025-01-01', TRUE);

-- =====================================================
-- 2. USUARIOS ADMINISTRATIVOS
-- =====================================================

-- Administradores
INSERT INTO usuarios (nombre, email, password_hash, rol, telefono, direccion, estado, acepto_politicas, politica_id) VALUES
('Maria Gonzalez', 'maria.gonzalez@jakake.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'administrador', '3101234567', 'Calle 15 #10-25', 'activo', TRUE, 1),
('Carlos Ramirez', 'carlos.ramirez@jakake.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'administrador', '3109876543', 'Carrera 8 #20-30', 'activo', TRUE, 1);

-- Cajeros
INSERT INTO usuarios (nombre, email, password_hash, rol, telefono, direccion, estado, acepto_politicas, politica_id) VALUES
('Laura Martinez', 'laura.martinez@jakake.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'cajero', '3201234567', 'Avenida 30 #15-40', 'activo', TRUE, 1),
('Pedro Sanchez', 'pedro.sanchez@jakake.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'cajero', '3159876543', 'Calle 25 #12-18', 'activo', TRUE, 1);

-- =====================================================
-- 3. CLIENTES
-- =====================================================

INSERT INTO clientes (nombre, cedula, telefono, direccion, email, fecha_nacimiento, acepto_politicas, politica_id) VALUES
('Juan Perez Garcia', '1088123456', '3151234567', 'Calle 10 #5-20, Pereira', 'juan.perez@email.com', '1990-05-15', TRUE, 1),
('Ana Maria Lopez', '1004567890', '3209876543', 'Carrera 7 #18-45, Pereira', 'ana.lopez@email.com', '1985-08-22', TRUE, 1),
('Roberto Castro', '1088234567', '3101112233', 'Avenida 30 #12-30, Pereira', 'roberto.castro@email.com', '1992-11-10', TRUE, 1),
('Sofia Hernandez', '1004678901', '3156667788', 'Calle 25 #8-15, Pereira', 'sofia.hernandez@email.com', '1988-03-05', TRUE, 1),
('Diego Morales', '1088345678', '3202223344', 'Carrera 15 #20-10, Pereira', 'diego.morales@email.com', '1995-07-18', TRUE, 1),
('Valentina Ruiz', '1004789012', '3158889999', 'Calle 40 #25-35, Pereira', 'valentina.ruiz@email.com', '2000-12-30', TRUE, 1),
('Andres Gomez', '1088456789', '3103334455', 'Avenida 4 #10-20, Pereira', 'andres.gomez@email.com', '1993-09-12', TRUE, 1),
('Carolina Silva', '1004890123', '3159990000', 'Carrera 12 #15-25, Pereira', 'carolina.silva@email.com', '1987-06-25', TRUE, 1),
('Miguel Torres', '1088567890', '3204445566', 'Calle 35 #18-40, Pereira', 'miguel.torres@email.com', '1991-04-08', TRUE, 1),
('Isabella Vargas', '1004901234', '3150001111', 'Avenida 20 #22-30, Pereira', 'isabella.vargas@email.com', '1998-10-15', TRUE, 1);

-- =====================================================
-- 4. PROVEEDORES
-- =====================================================

INSERT INTO proveedores (nombre, nit, direccion, telefono, contacto, ciudad, email, estado) VALUES
('Distribuidora Nacional S.A.', '900123456-1', 'Zona Industrial Calle 50 #30-10', '6013456789', 'Luis Fernandez', 'Bogota', 'ventas@disnacional.com', 'activo'),
('Papeles y Suministros Ltda.', '800234567-2', 'Carrera 100 #15-20', '6047890123', 'Martha Perez', 'Medellin', 'contacto@papelesysuminitros.com', 'activo'),
('Arte y Creatividad S.A.S.', '900345678-3', 'Avenida 6 #25-30', '6022345678', 'Carlos Mendoza', 'Cali', 'ventas@arteycreatividad.com', 'activo'),
('Escolar Total', '800456789-4', 'Calle 80 #40-15', '6013567890', 'Ana Garcia', 'Bogota', 'pedidos@escolartotal.com', 'activo'),
('Oficina Moderna', '900567890-5', 'Carrera 50 #20-25', '6044567890', 'Roberto Diaz', 'Medellin', 'info@oficinamoderna.com', 'activo');

-- =====================================================
-- 5. PRODUCTOS
-- =====================================================

-- Productos Escolares
INSERT INTO productos (codigo, nombre, descripcion, precio, cantidad, cantidad_minima, proveedor_id, tipo, estado, fecha_ingreso) VALUES
('ESC001', 'Cuaderno Norma 100 Hojas', 'Cuaderno cuadriculado 100 hojas tamaño carta', 4500, 150, 20, 4, 'escolar', 'activo', '2025-01-15'),
('ESC002', 'Lápiz Mirado HB', 'Lápiz de grafito HB punta redonda', 1200, 300, 50, 4, 'escolar', 'activo', '2025-01-15'),
('ESC003', 'Borrador Blanco', 'Borrador de nata blanco suave', 800, 200, 30, 4, 'escolar', 'activo', '2025-01-15'),
('ESC004', 'Tajapuntas Metálico', 'Tajapuntas de metal con depósito', 1500, 100, 15, 4, 'escolar', 'activo', '2025-01-15'),
('ESC005', 'Colores Norma x12', 'Caja de colores Norma 12 unidades', 8500, 80, 15, 4, 'escolar', 'activo', '2025-01-15'),
('ESC006', 'Marcadores x6', 'Set de marcadores punta gruesa 6 colores', 6500, 60, 10, 4, 'escolar', 'activo', '2025-01-20'),
('ESC007', 'Cartulina Blanca', 'Cartulina blanca tamaño pliego', 1000, 250, 40, 2, 'escolar', 'activo', '2025-01-20'),
('ESC008', 'Cartulina Colores', 'Cartulina de colores surtidos tamaño pliego', 1200, 200, 35, 2, 'escolar', 'activo', '2025-01-20'),
('ESC009', 'Block Iris', 'Block de hojas iris tamaño carta x50', 5500, 90, 15, 2, 'escolar', 'activo', '2025-01-22'),
('ESC010', 'Regla 30cm', 'Regla plástica transparente 30cm', 2000, 120, 20, 4, 'escolar', 'activo', '2025-01-22');

-- Productos de Oficina
INSERT INTO productos (codigo, nombre, descripcion, precio, cantidad, cantidad_minima, proveedor_id, tipo, estado, fecha_ingreso) VALUES
('OFI001', 'Resma Papel Carta', 'Resma papel bond blanco carta x500', 12000, 100, 15, 1, 'oficina', 'activo', '2025-01-10'),
('OFI002', 'Carpeta Legajadora', 'Carpeta legajadora tamaño oficio', 3500, 80, 12, 5, 'oficina', 'activo', '2025-01-10'),
('OFI003', 'Ganchos Legajador x50', 'Caja de ganchos legajadores metálicos', 4000, 60, 10, 5, 'oficina', 'activo', '2025-01-12'),
('OFI004', 'Clips Mariposa x100', 'Clips mariposa surtidos caja x100', 3000, 70, 12, 5, 'oficina', 'activo', '2025-01-12'),
('OFI005', 'Esfero Bic Azul', 'Esfero Bic punta fina azul', 1500, 250, 40, 1, 'oficina', 'activo', '2025-01-15'),
('OFI006', 'Esfero Bic Negro', 'Esfero Bic punta fina negro', 1500, 250, 40, 1, 'oficina', 'activo', '2025-01-15'),
('OFI007', 'Resaltador Amarillo', 'Resaltador fluorescente amarillo', 2500, 100, 15, 1, 'oficina', 'activo', '2025-01-18'),
('OFI008', 'Notas Adhesivas', 'Bloque notas adhesivas 76x76mm amarillo', 4500, 90, 15, 5, 'oficina', 'activo', '2025-01-18'),
('OFI009', 'Corrector Líquido', 'Corrector líquido tipo lapicero', 3500, 80, 12, 5, 'oficina', 'activo', '2025-01-20'),
('OFI010', 'Archivador AZ', 'Archivador de palanca tamaño oficio', 8500, 50, 8, 5, 'oficina', 'activo', '2025-01-20');

-- Productos de Arte
INSERT INTO productos (codigo, nombre, descripcion, precio, cantidad, cantidad_minima, proveedor_id, tipo, estado, fecha_ingreso) VALUES
('ART001', 'Temperas x6', 'Set temperas escolares 6 colores', 9500, 45, 8, 3, 'arte', 'activo', '2025-01-12'),
('ART002', 'Pinceles x3', 'Set de pinceles pelo sintético', 7500, 40, 8, 3, 'arte', 'activo', '2025-01-12'),
('ART003', 'Plastilina x6', 'Caja plastilina 6 colores', 5500, 55, 10, 3, 'arte', 'activo', '2025-01-15'),
('ART004', 'Tijera Escolar', 'Tijera punta roma para niños', 3500, 90, 15, 3, 'arte', 'activo', '2025-01-15'),
('ART005', 'Pegamento Barra', 'Pegamento en barra 40gr', 3000, 100, 18, 3, 'arte', 'activo', '2025-01-18'),
('ART006', 'Silicona Líquida', 'Silicona líquida 250ml', 4500, 70, 12, 3, 'arte', 'activo', '2025-01-18'),
('ART007', 'Escarcha x6', 'Set escarcha colores surtidos', 6500, 35, 8, 3, 'arte', 'activo', '2025-01-20'),
('ART008', 'Foamy Colores x10', 'Láminas de foamy colores x10', 8000, 50, 10, 3, 'arte', 'activo', '2025-01-20'),
('ART009', 'Papel Silueta', 'Papel silueta colores surtidos x20', 7000, 40, 8, 2, 'arte', 'activo', '2025-01-22'),
('ART010', 'Acuarelas x12', 'Set acuarelas 12 colores con pincel', 12000, 30, 6, 3, 'arte', 'activo', '2025-01-22');

-- =====================================================
-- 6. VENTAS Y DETALLES
-- =====================================================

-- Venta 1 - Cliente 1 (Juan Pérez)
INSERT INTO ventas (fecha, cliente_id, usuario_id, subtotal, impuesto, total, estado) 
VALUES ('2025-01-25', 1, 3, 25000, 4750, 29750, 'completada');

INSERT INTO detalle_venta (venta_id, producto_id, cantidad, precio_unitario, subtotal) VALUES
(1, 1, 3, 4500, 13500),
(1, 2, 5, 1200, 6000),
(1, 3, 3, 800, 2400),
(1, 5, 1, 8500, 8500);

-- Venta 2 - Cliente 2 (Ana María)
INSERT INTO ventas (fecha, cliente_id, usuario_id, subtotal, impuesto, total, estado) 
VALUES ('2025-01-26', 2, 3, 45000, 8550, 53550, 'completada');

INSERT INTO detalle_venta (venta_id, producto_id, cantidad, precio_unitario, subtotal) VALUES
(2, 11, 2, 12000, 24000),
(2, 15, 10, 1500, 15000),
(2, 17, 2, 2500, 5000);

-- Venta 3 - Cliente 3 (Roberto Castro)
INSERT INTO ventas (fecha, cliente_id, usuario_id, subtotal, impuesto, total, estado) 
VALUES ('2025-01-28', 3, 4, 32000, 6080, 38080, 'completada');

INSERT INTO detalle_venta (venta_id, producto_id, cantidad, precio_unitario, subtotal) VALUES
(3, 21, 2, 9500, 19000),
(3, 22, 1, 7500, 7500),
(3, 25, 1, 3000, 3000),
(3, 26, 1, 4500, 4500);

-- Venta 4 - Cliente 4 (Sofia Hernández)
INSERT INTO ventas (fecha, cliente_id, usuario_id, subtotal, impuesto, total, estado) 
VALUES ('2025-02-01', 4, 3, 18500, 3515, 22015, 'completada');

INSERT INTO detalle_venta (venta_id, producto_id, cantidad, precio_unitario, subtotal) VALUES
(4, 7, 10, 1000, 10000),
(4, 8, 5, 1200, 6000),
(4, 4, 1, 1500, 1500);

-- Venta 5 - Cliente 5 (Diego Morales)
INSERT INTO ventas (fecha, cliente_id, usuario_id, subtotal, impuesto, total, estado) 
VALUES ('2025-02-03', 5, 4, 51000, 9690, 60690, 'completada');

INSERT INTO detalle_venta (venta_id, producto_id, cantidad, precio_unitario, subtotal) VALUES
(5, 20, 5, 8500, 42500),
(5, 12, 2, 3500, 7000),
(5, 14, 1, 3000, 3000);

-- Venta 6 - Sin cliente registrado
INSERT INTO ventas (fecha, cliente_id, usuario_id, subtotal, impuesto, total, estado) 
VALUES ('2025-02-05', NULL, 3, 15000, 2850, 17850, 'completada');

INSERT INTO detalle_venta (venta_id, producto_id, cantidad, precio_unitario, subtotal) VALUES
(6, 2, 10, 1200, 12000),
(6, 3, 5, 800, 4000);

-- Venta 7 - Cliente 6 (Valentina Ruiz)
INSERT INTO ventas (fecha, cliente_id, usuario_id, subtotal, impuesto, total, estado) 
VALUES ('2025-02-07', 6, 4, 38000, 7220, 45220, 'completada');

INSERT INTO detalle_venta (venta_id, producto_id, cantidad, precio_unitario, subtotal) VALUES
(7, 1, 4, 4500, 18000),
(7, 5, 2, 8500, 17000),
(7, 10, 1, 2000, 2000);

-- Venta 8 - Cliente 7 (Andrés Gómez)
INSERT INTO ventas (fecha, cliente_id, usuario_id, subtotal, impuesto, total, estado) 
VALUES ('2025-02-10', 7, 3, 27500, 5225, 32725, 'completada');

INSERT INTO detalle_venta (venta_id, producto_id, cantidad, precio_unitario, subtotal) VALUES
(8, 11, 1, 12000, 12000),
(8, 16, 8, 1500, 12000),
(8, 19, 1, 3500, 3500);

-- Venta 9 - Cliente 8 (Carolina Silva)
INSERT INTO ventas (fecha, cliente_id, usuario_id, subtotal, impuesto, total, estado) 
VALUES ('2025-02-12', 8, 4, 42000, 7980, 49980, 'completada');

INSERT INTO detalle_venta (venta_id, producto_id, cantidad, precio_unitario, subtotal) VALUES
(9, 30, 3, 12000, 36000),
(9, 24, 1, 3500, 3500),
(9, 27, 1, 6500, 6500);

-- Venta 10 - Cliente 9 (Miguel Torres)
INSERT INTO ventas (fecha, cliente_id, usuario_id, subtotal, impuesto, total, estado) 
VALUES ('2025-02-15', 9, 3, 34500, 6555, 41055, 'completada');

INSERT INTO detalle_venta (venta_id, producto_id, cantidad, precio_unitario, subtotal) VALUES
(10, 13, 5, 4000, 20000),
(10, 18, 2, 4500, 9000),
(10, 9, 1, 5500, 5500);

-- Venta 11 - Cliente 10 (Isabella Vargas)
INSERT INTO ventas (fecha, cliente_id, usuario_id, subtotal, impuesto, total, estado) 
VALUES ('2025-02-18', 10, 4, 29000, 5510, 34510, 'completada');

INSERT INTO detalle_venta (venta_id, producto_id, cantidad, precio_unitario, subtotal) VALUES
(11, 21, 1, 9500, 9500),
(11, 23, 2, 5500, 11000),
(11, 28, 1, 8000, 8000);

-- Venta 12 - Cliente 1 (Juan - segunda compra)
INSERT INTO ventas (fecha, cliente_id, usuario_id, subtotal, impuesto, total, estado) 
VALUES ('2025-02-20', 1, 3, 19500, 3705, 23205, 'completada');

INSERT INTO detalle_venta (venta_id, producto_id, cantidad, precio_unitario, subtotal) VALUES
(12, 6, 2, 6500, 13000),
(12, 7, 5, 1000, 5000),
(12, 4, 1, 1500, 1500);

-- Venta 13 - Cliente 3 (Roberto - segunda compra)
INSERT INTO ventas (fecha, cliente_id, usuario_id, subtotal, impuesto, total, estado) 
VALUES ('2025-02-22', 3, 4, 41000, 7790, 48790, 'completada');

INSERT INTO detalle_venta (venta_id, producto_id, cantidad, precio_unitario, subtotal) VALUES
(13, 11, 2, 12000, 24000),
(13, 15, 6, 1500, 9000),
(13, 20, 1, 8500, 8500);

-- Venta 14 - Cliente 5 (Diego - segunda compra)
INSERT INTO ventas (fecha, cliente_id, usuario_id, subtotal, impuesto, total, estado) 
VALUES ('2025-02-25', 5, 3, 36500, 6935, 43435, 'completada');

INSERT INTO detalle_venta (venta_id, producto_id, cantidad, precio_unitario, subtotal) VALUES
(14, 1, 5, 4500, 22500),
(14, 2, 8, 1200, 9600),
(14, 10, 2, 2000, 4000);

-- Venta 15 - Sin cliente
INSERT INTO ventas (fecha, cliente_id, usuario_id, subtotal, impuesto, total, estado) 
VALUES ('2025-02-27', NULL, 4, 21000, 3990, 24990, 'completada');

INSERT INTO detalle_venta (venta_id, producto_id, cantidad, precio_unitario, subtotal) VALUES
(15, 5, 2, 8500, 17000),
(15, 3, 5, 800, 4000);

-- =====================================================
-- 7. DEVOLUCIONES (generarán bonos automáticamente)
-- =====================================================

-- Devolución 1 - Cliente 1 devuelve cuadernos de venta 1
INSERT INTO devoluciones (venta_id, producto_id, cantidad, motivo, valor_devolucion, usuario_id, fecha_devolucion)
VALUES (1, 1, 1, 'Producto con defecto en el empaste', 4500, 3, '2025-01-27');

-- Devolución 2 - Cliente 2 devuelve resma de venta 2
INSERT INTO devoluciones (venta_id, producto_id, cantidad, motivo, valor_devolucion, usuario_id, fecha_devolucion)
VALUES (2, 11, 1, 'Cliente compró de más', 12000, 3, '2025-01-28');

-- Devolución 3 - Cliente 4 devuelve cartulinas de venta 4
INSERT INTO devoluciones (venta_id, producto_id, cantidad, motivo, valor_devolucion, usuario_id, fecha_devolucion)
VALUES (4, 7, 3, 'Color no era el esperado', 3000, 4, '2025-02-02');

-- =====================================================
-- 8. REGISTROS DE ACEPTACIÓN DE POLÍTICAS
-- =====================================================

-- Aceptación de políticas por usuarios
INSERT INTO aceptacion_politicas (politica_id, tipo_usuario, usuario_id, cliente_id, ip_aceptacion) VALUES
(1, 'usuario', 1, NULL, '192.168.1.10'),
(1, 'usuario', 2, NULL, '192.168.1.11'),
(1, 'usuario', 3, NULL, '192.168.1.12'),
(1, 'usuario', 4, NULL, '192.168.1.13');

-- Aceptación de políticas por clientes
INSERT INTO aceptacion_politicas (politica_id, tipo_usuario, usuario_id, cliente_id, ip_aceptacion) VALUES
(1, 'cliente', NULL, 1, '192.168.1.20'),
(1, 'cliente', NULL, 2, '192.168.1.21'),
(1, 'cliente', NULL, 3, '192.168.1.22'),
(1, 'cliente', NULL, 4, '192.168.1.23'),
(1, 'cliente', NULL, 5, '192.168.1.24'),
(1, 'cliente', NULL, 6, '192.168.1.25'),
(1, 'cliente', NULL, 7, '192.168.1.26'),
(1, 'cliente', NULL, 8, '192.168.1.27'),
(1, 'cliente', NULL, 9, '192.168.1.28'),
(1, 'cliente', NULL, 10, '192.168.1.29');

-- =====================================================
-- VERIFICACIÓN DE DATOS INSERTADOS
-- =====================================================

SELECT 'Datos de prueba insertados exitosamente' AS mensaje;

SELECT 'RESUMEN DE DATOS:' AS seccion;
SELECT 'Políticas' AS tabla, COUNT(*) AS registros FROM politicas_datos
UNION ALL SELECT 'Usuarios', COUNT(*) FROM usuarios
UNION ALL SELECT 'Clientes', COUNT(*) FROM clientes
UNION ALL SELECT 'Proveedores', COUNT(*) FROM proveedores
UNION ALL SELECT 'Productos', COUNT(*) FROM productos
UNION ALL SELECT 'Ventas', COUNT(*) FROM ventas
UNION ALL SELECT 'Detalles de Venta', COUNT(*) FROM detalle_venta
UNION ALL SELECT 'Devoluciones', COUNT(*) FROM devoluciones
UNION ALL SELECT 'Bonos Regalo', COUNT(*) FROM bonos_regalo
UNION ALL SELECT 'Movimientos Inventario', COUNT(*) FROM movimientos_inventario
UNION ALL SELECT 'Aceptación Políticas', COUNT(*) FROM aceptacion_politicas
UNION ALL SELECT 'Auditoría', COUNT(*) FROM auditoria;

-- Ver bonos generados automáticamente por triggers
SELECT 'BONOS GENERADOS AUTOMÁTICAMENTE:' AS info;
SELECT 
    b.codigo,
    c.nombre AS cliente,
    b.valor,
    b.estado,
    b.fecha_emision,
    b.fecha_vencimiento
FROM bonos_regalo b
INNER JOIN clientes c ON b.cliente_id = c.id;
