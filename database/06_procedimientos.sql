-- =====================================================
-- PROCEDIMIENTOS ALMACENADOS
-- Sistema de Base de Datos - Papelería JAKAKE
-- =====================================================
-- Descripción: Procedimientos para operaciones complejas
--              de ventas, devoluciones, reportes y consultas
-- Autor: Persona 1
-- Fecha: 2025-11-27
-- =====================================================

USE papeleria_jakake;

DELIMITER $$

-- =====================================================
-- PROCEDIMIENTO: Calcular totales de venta
-- Calcula subtotal, IVA (19%) y total
-- =====================================================

DROP PROCEDURE IF EXISTS sp_calcular_totales_venta$$

CREATE PROCEDURE sp_calcular_totales_venta(
    IN p_subtotal DECIMAL(10,2),
    OUT p_impuesto DECIMAL(10,2),
    OUT p_total DECIMAL(10,2)
)
BEGIN
    -- Calcular IVA (19%)
    SET p_impuesto = ROUND(p_subtotal * 0.19, 2);
    
    -- Calcular total
    SET p_total = p_subtotal + p_impuesto;
END$$

-- =====================================================
-- PROCEDIMIENTO: Registrar venta completa
-- Registra una venta con múltiples productos en transacción
-- Parámetros de entrada:
--   - p_cliente_id: ID del cliente (puede ser NULL)
--   - p_usuario_id: ID del cajero
--   - p_productos: JSON con array de productos
--     Formato: [{"producto_id": 1, "cantidad": 2, "precio_unitario": 1500}, ...]
-- Parámetros de salida:
--   - p_venta_id: ID de la venta creada
--   - p_total_final: Total de la venta
-- =====================================================

DROP PROCEDURE IF EXISTS sp_registrar_venta_completa$$

CREATE PROCEDURE sp_registrar_venta_completa(
    IN p_cliente_id INT,
    IN p_usuario_id INT,
    IN p_productos JSON,
    OUT p_venta_id INT,
    OUT p_total_final DECIMAL(10,2)
)
BEGIN
    DECLARE v_subtotal DECIMAL(10,2) DEFAULT 0;
    DECLARE v_impuesto DECIMAL(10,2);
    DECLARE v_total DECIMAL(10,2);
    DECLARE v_producto_id INT;
    DECLARE v_cantidad INT;
    DECLARE v_precio_unitario DECIMAL(10,2);
    DECLARE v_subtotal_detalle DECIMAL(10,2);
    DECLARE v_index INT DEFAULT 0;
    DECLARE v_array_length INT;
    DECLARE v_producto_json JSON;
    
    -- Declarar handler para errores
    DECLARE EXIT HANDLER FOR SQLEXCEPTION
    BEGIN
        ROLLBACK;
        SIGNAL SQLSTATE '45000'
        SET MESSAGE_TEXT = 'Error al registrar la venta. Transacción revertida.';
    END;
    
    -- Iniciar transacción
    START TRANSACTION;
    
    -- Validar que haya productos
    SET v_array_length = JSON_LENGTH(p_productos);
    IF v_array_length = 0 THEN
        SIGNAL SQLSTATE '45000'
        SET MESSAGE_TEXT = 'Error: Debe incluir al menos un producto en la venta';
    END IF;
    
    -- Calcular subtotal recorriendo el JSON
    WHILE v_index < v_array_length DO
        SET v_producto_json = JSON_EXTRACT(p_productos, CONCAT('$[', v_index, ']'));
        SET v_producto_id = JSON_UNQUOTE(JSON_EXTRACT(v_producto_json, '$.producto_id'));
        SET v_cantidad = JSON_UNQUOTE(JSON_EXTRACT(v_producto_json, '$.cantidad'));
        SET v_precio_unitario = JSON_UNQUOTE(JSON_EXTRACT(v_producto_json, '$.precio_unitario'));
        
        -- Validar que el producto existe
        IF NOT EXISTS (SELECT 1 FROM productos WHERE id = v_producto_id AND estado = 'activo') THEN
            SIGNAL SQLSTATE '45000'
            SET MESSAGE_TEXT = CONCAT('Error: El producto con ID ', v_producto_id, ' no existe o está inactivo');
        END IF;
        
        -- Calcular subtotal del detalle
        SET v_subtotal_detalle = v_cantidad * v_precio_unitario;
        SET v_subtotal = v_subtotal + v_subtotal_detalle;
        
        SET v_index = v_index + 1;
    END WHILE;
    
    -- Calcular impuesto y total
    CALL sp_calcular_totales_venta(v_subtotal, v_impuesto, v_total);
    
    -- Insertar venta
    INSERT INTO ventas (
        fecha,
        cliente_id,
        usuario_id,
        subtotal,
        impuesto,
        total,
        estado
    ) VALUES (
        CURDATE(),
        p_cliente_id,
        p_usuario_id,
        v_subtotal,
        v_impuesto,
        v_total,
        'completada'
    );
    
    SET p_venta_id = LAST_INSERT_ID();
    SET p_total_final = v_total;
    
    -- Insertar detalles de venta
    SET v_index = 0;
    WHILE v_index < v_array_length DO
        SET v_producto_json = JSON_EXTRACT(p_productos, CONCAT('$[', v_index, ']'));
        SET v_producto_id = JSON_UNQUOTE(JSON_EXTRACT(v_producto_json, '$.producto_id'));
        SET v_cantidad = JSON_UNQUOTE(JSON_EXTRACT(v_producto_json, '$.cantidad'));
        SET v_precio_unitario = JSON_UNQUOTE(JSON_EXTRACT(v_producto_json, '$.precio_unitario'));
        SET v_subtotal_detalle = v_cantidad * v_precio_unitario;
        
        INSERT INTO detalle_venta (
            venta_id,
            producto_id,
            cantidad,
            precio_unitario,
            subtotal
        ) VALUES (
            p_venta_id,
            v_producto_id,
            v_cantidad,
            v_precio_unitario,
            v_subtotal_detalle
        );
        
        SET v_index = v_index + 1;
    END WHILE;
    
    -- Confirmar transacción
    COMMIT;
END$$

-- =====================================================
-- PROCEDIMIENTO: Procesar devolución
-- Procesa una devolución y genera bono automáticamente
-- =====================================================

DROP PROCEDURE IF EXISTS sp_procesar_devolucion$$

CREATE PROCEDURE sp_procesar_devolucion(
    IN p_venta_id INT,
    IN p_producto_id INT,
    IN p_cantidad INT,
    IN p_motivo TEXT,
    IN p_usuario_id INT,
    OUT p_devolucion_id INT,
    OUT p_bono_codigo VARCHAR(50)
)
BEGIN
    DECLARE v_precio_venta DECIMAL(10,2);
    DECLARE v_valor_devolucion DECIMAL(10,2);
    
    -- Declarar handler para errores
    DECLARE EXIT HANDLER FOR SQLEXCEPTION
    BEGIN
        ROLLBACK;
        SIGNAL SQLSTATE '45000'
        SET MESSAGE_TEXT = 'Error al procesar la devolución. Transacción revertida.';
    END;
    
    -- Iniciar transacción
    START TRANSACTION;
    
    -- Obtener precio del producto en la venta
    SELECT precio_unitario INTO v_precio_venta
    FROM detalle_venta
    WHERE venta_id = p_venta_id AND producto_id = p_producto_id;
    
    IF v_precio_venta IS NULL THEN
        SIGNAL SQLSTATE '45000'
        SET MESSAGE_TEXT = 'Error: El producto no fue vendido en esta venta';
    END IF;
    
    -- Calcular valor de devolución
    SET v_valor_devolucion = p_cantidad * v_precio_venta;
    
    -- Registrar devolución
    INSERT INTO devoluciones (
        venta_id,
        producto_id,
        cantidad,
        motivo,
        valor_devolucion,
        usuario_id,
        fecha_devolucion
    ) VALUES (
        p_venta_id,
        p_producto_id,
        p_cantidad,
        p_motivo,
        v_valor_devolucion,
        p_usuario_id,
        CURDATE()
    );
    
    SET p_devolucion_id = LAST_INSERT_ID();
    
    -- El trigger tr_generar_bono_devolucion creará el bono automáticamente
    -- Obtener el código del bono generado
    SELECT codigo INTO p_bono_codigo
    FROM bonos_regalo
    WHERE devolucion_id = p_devolucion_id;
    
    -- Confirmar transacción
    COMMIT;
END$$

-- =====================================================
-- PROCEDIMIENTO: Historial de compras de cliente
-- Muestra todas las compras de un cliente
-- =====================================================

DROP PROCEDURE IF EXISTS sp_historial_compras_cliente$$

CREATE PROCEDURE sp_historial_compras_cliente(
    IN p_cliente_id INT
)
BEGIN
    SELECT 
        v.id AS venta_id,
        v.fecha,
        v.total,
        v.estado,
        GROUP_CONCAT(
            CONCAT(p.nombre, ' (', dv.cantidad, ')')
            SEPARATOR ', '
        ) AS productos,
        CONCAT(u.nombre) AS cajero
    FROM ventas v
    INNER JOIN detalle_venta dv ON v.id = dv.venta_id
    INNER JOIN productos p ON dv.producto_id = p.id
    INNER JOIN usuarios u ON v.usuario_id = u.id
    WHERE v.cliente_id = p_cliente_id
    GROUP BY v.id, v.fecha, v.total, v.estado, u.nombre
    ORDER BY v.fecha DESC;
END$$

-- =====================================================
-- PROCEDIMIENTO: Reporte de ventas por período
-- Muestra ventas entre dos fechas
-- =====================================================

DROP PROCEDURE IF EXISTS sp_reporte_ventas_periodo$$

CREATE PROCEDURE sp_reporte_ventas_periodo(
    IN p_fecha_inicio DATE,
    IN p_fecha_fin DATE
)
BEGIN
    SELECT 
        v.id,
        v.fecha,
        COALESCE(c.nombre, 'Cliente no registrado') AS cliente,
        u.nombre AS cajero,
        v.subtotal,
        v.impuesto,
        v.total,
        v.estado,
        COUNT(dv.id) AS cantidad_productos
    FROM ventas v
    LEFT JOIN clientes c ON v.cliente_id = c.id
    INNER JOIN usuarios u ON v.usuario_id = u.id
    LEFT JOIN detalle_venta dv ON v.id = dv.venta_id
    WHERE v.fecha BETWEEN p_fecha_inicio AND p_fecha_fin
    GROUP BY v.id, v.fecha, c.nombre, u.nombre, v.subtotal, v.impuesto, v.total, v.estado
    ORDER BY v.fecha DESC, v.id DESC;
    
    -- Resumen del período
    SELECT 
        COUNT(*) AS total_ventas,
        SUM(subtotal) AS total_subtotal,
        SUM(impuesto) AS total_impuesto,
        SUM(total) AS total_ingresos,
        AVG(total) AS promedio_venta
    FROM ventas
    WHERE fecha BETWEEN p_fecha_inicio AND p_fecha_fin
      AND estado = 'completada';
END$$

-- =====================================================
-- PROCEDIMIENTO: Productos con stock bajo
-- Lista productos por debajo de cantidad mínima
-- =====================================================

DROP PROCEDURE IF EXISTS sp_productos_bajo_stock$$

CREATE PROCEDURE sp_productos_bajo_stock(
    IN p_cantidad_minima INT
)
BEGIN
    SELECT 
        p.id,
        p.codigo,
        p.nombre,
        p.cantidad AS stock_actual,
        p.cantidad_minima,
        (p.cantidad_minima - p.cantidad) AS unidades_faltantes,
        prov.nombre AS proveedor,
        prov.telefono AS telefono_proveedor,
        p.precio,
        (p.cantidad_minima - p.cantidad) * p.precio AS valor_pedido_sugerido
    FROM productos p
    INNER JOIN proveedores prov ON p.proveedor_id = prov.id
    WHERE p.cantidad <= p_cantidad_minima
      AND p.estado = 'activo'
    ORDER BY (p.cantidad_minima - p.cantidad) DESC;
END$$

-- =====================================================
-- PROCEDIMIENTO: Auditoría reciente
-- Muestra registros de auditoría de los últimos días
-- =====================================================

DROP PROCEDURE IF EXISTS sp_auditoria_reciente$$

CREATE PROCEDURE sp_auditoria_reciente(
    IN p_dias INT
)
BEGIN
    SELECT 
        a.id,
        a.tabla_afectada,
        a.operacion,
        a.registro_id,
        COALESCE(u.nombre, 'Sistema') AS usuario,
        a.fecha_hora,
        a.valores_anteriores,
        a.valores_nuevos
    FROM auditoria a
    LEFT JOIN usuarios u ON a.usuario_id = u.id
    WHERE a.fecha_hora >= DATE_SUB(NOW(), INTERVAL p_dias DAY)
    ORDER BY a.fecha_hora DESC
    LIMIT 100;
END$$

-- =====================================================
-- PROCEDIMIENTO: Top productos más vendidos
-- Muestra los productos más vendidos en un período
-- =====================================================

DROP PROCEDURE IF EXISTS sp_productos_mas_vendidos$$

CREATE PROCEDURE sp_productos_mas_vendidos(
    IN p_fecha_inicio DATE,
    IN p_fecha_fin DATE,
    IN p_limite INT
)
BEGIN
    SELECT 
        p.id,
        p.codigo,
        p.nombre,
        p.tipo,
        SUM(dv.cantidad) AS total_vendido,
        COUNT(DISTINCT dv.venta_id) AS numero_ventas,
        SUM(dv.subtotal) AS ingresos_generados,
        p.cantidad AS stock_actual,
        prov.nombre AS proveedor
    FROM detalle_venta dv
    INNER JOIN productos p ON dv.producto_id = p.id
    INNER JOIN ventas v ON dv.venta_id = v.id
    INNER JOIN proveedores prov ON p.proveedor_id = prov.id
    WHERE v.fecha BETWEEN p_fecha_inicio AND p_fecha_fin
      AND v.estado = 'completada'
    GROUP BY p.id, p.codigo, p.nombre, p.tipo, p.cantidad, prov.nombre
    ORDER BY total_vendido DESC
    LIMIT p_limite;
END$$

-- =====================================================
-- PROCEDIMIENTO: Resumen de cliente
-- Información completa de un cliente
-- =====================================================

DROP PROCEDURE IF EXISTS sp_resumen_cliente$$

CREATE PROCEDURE sp_resumen_cliente(
    IN p_cliente_id INT
)
BEGIN
    -- Información del cliente
    SELECT 
        c.id,
        c.nombre,
        c.cedula,
        c.telefono,
        c.email,
        c.direccion,
        c.fecha_registro,
        TIMESTAMPDIFF(YEAR, c.fecha_nacimiento, CURDATE()) AS edad
    FROM clientes c
    WHERE c.id = p_cliente_id;
    
    -- Estadísticas de compras
    SELECT 
        COUNT(*) AS total_compras,
        SUM(total) AS total_gastado,
        AVG(total) AS promedio_compra,
        MAX(fecha) AS ultima_compra,
        MIN(fecha) AS primera_compra
    FROM ventas
    WHERE cliente_id = p_cliente_id
      AND estado = 'completada';
    
    -- Bonos activos
    SELECT 
        codigo,
        valor,
        fecha_emision,
        fecha_vencimiento,
        DATEDIFF(fecha_vencimiento, CURDATE()) AS dias_vigencia
    FROM bonos_regalo
    WHERE cliente_id = p_cliente_id
      AND estado = 'activo'
      AND fecha_vencimiento >= CURDATE()
    ORDER BY fecha_vencimiento;
END$$

-- =====================================================
-- PROCEDIMIENTO: Aplicar bono a venta
-- Aplica un bono de regalo a una venta
-- =====================================================

DROP PROCEDURE IF EXISTS sp_aplicar_bono_venta$$

CREATE PROCEDURE sp_aplicar_bono_venta(
    IN p_bono_codigo VARCHAR(50),
    IN p_venta_id INT
)
BEGIN
    DECLARE v_bono_id INT;
    DECLARE v_valor_bono DECIMAL(10,2);
    DECLARE v_estado_bono VARCHAR(20);
    
    -- Declarar handler para errores
    DECLARE EXIT HANDLER FOR SQLEXCEPTION
    BEGIN
        ROLLBACK;
        SIGNAL SQLSTATE '45000'
        SET MESSAGE_TEXT = 'Error al aplicar el bono. Transacción revertida.';
    END;
    
    START TRANSACTION;
    
    -- Obtener información del bono
    SELECT id, valor, estado INTO v_bono_id, v_valor_bono, v_estado_bono
    FROM bonos_regalo
    WHERE codigo = p_bono_codigo;
    
    IF v_bono_id IS NULL THEN
        SIGNAL SQLSTATE '45000'
        SET MESSAGE_TEXT = 'Error: El código de bono no existe';
    END IF;
    
    IF v_estado_bono != 'activo' THEN
        SIGNAL SQLSTATE '45000'
        SET MESSAGE_TEXT = 'Error: El bono no está activo';
    END IF;
    
    -- Marcar bono como usado
    UPDATE bonos_regalo
    SET estado = 'usado',
        fecha_uso = CURDATE(),
        venta_uso_id = p_venta_id
    WHERE id = v_bono_id;
    
    -- Actualizar total de la venta (restar valor del bono)
    UPDATE ventas
    SET total = total - v_valor_bono
    WHERE id = p_venta_id;
    
    COMMIT;
    
    SELECT CONCAT('Bono aplicado exitosamente. Descuento: $', v_valor_bono) AS mensaje;
END$$

DELIMITER ;

-- =====================================================
-- VERIFICACIÓN DE PROCEDIMIENTOS CREADOS
-- =====================================================

SELECT 
    ROUTINE_NAME AS procedimiento,
    ROUTINE_TYPE AS tipo,
    CREATED AS fecha_creacion
FROM information_schema.ROUTINES
WHERE ROUTINE_SCHEMA = 'papeleria_jakake'
  AND ROUTINE_TYPE = 'PROCEDURE'
ORDER BY ROUTINE_NAME;

SELECT 'Procedimientos almacenados creados exitosamente' AS mensaje;
SELECT COUNT(*) AS total_procedimientos
FROM information_schema.ROUTINES
WHERE ROUTINE_SCHEMA = 'papeleria_jakake'
  AND ROUTINE_TYPE = 'PROCEDURE';
