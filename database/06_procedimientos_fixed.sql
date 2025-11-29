-- =====================================================
-- PROCEDIMIENTOS ALMACENADOS CORREGIDOS
-- Sistema de Base de Datos - Papelería JAKAKE
-- =====================================================

USE papeleria_jakake;

-- Eliminar procedimientos existentes
DROP PROCEDURE IF EXISTS sp_productos_bajo_stock;
DROP PROCEDURE IF EXISTS sp_productos_mas_vendidos;
DROP PROCEDURE IF EXISTS sp_registrar_venta_completa;
DROP PROCEDURE IF EXISTS sp_calcular_totales_venta;

DELIMITER $$

-- =====================================================
-- PROCEDIMIENTO: Productos con bajo stock
-- =====================================================
CREATE PROCEDURE sp_productos_bajo_stock()
BEGIN
    SELECT 
        p.id,
        p.codigo,
        p.nombre,
        p.cantidad,
        p.cantidad_minima,
        pr.nombre as proveedor
    FROM productos p
    LEFT JOIN proveedores pr ON p.proveedor_id = pr.id
    WHERE p.cantidad <= p.cantidad_minima 
      AND p.estado = 'activo'
    ORDER BY p.cantidad ASC
    LIMIT 10;
END$$

-- =====================================================
-- PROCEDIMIENTO: Productos más vendidos
-- =====================================================
CREATE PROCEDURE sp_productos_mas_vendidos(IN p_limite INT)
BEGIN
    SELECT 
        p.id,
        p.codigo,
        p.nombre,
        p.tipo,
        SUM(dv.cantidad) as total_vendido,
        COUNT(DISTINCT dv.venta_id) as num_ventas,
        SUM(dv.cantidad * dv.precio_unitario) as ingresos_totales
    FROM productos p
    INNER JOIN detalle_venta dv ON p.id = dv.producto_id
    INNER JOIN ventas v ON dv.venta_id = v.id
    WHERE v.estado = 'completada'
    GROUP BY p.id, p.codigo, p.nombre, p.tipo
    ORDER BY total_vendido DESC
    LIMIT p_limite;
END$$

-- =====================================================
-- PROCEDIMIENTO: Calcular totales de venta
-- =====================================================
CREATE PROCEDURE sp_calcular_totales_venta(
    IN p_subtotal DECIMAL(10,2),
    OUT p_impuesto DECIMAL(10,2),
    OUT p_total DECIMAL(10,2)
)
BEGIN
    SET p_impuesto = ROUND(p_subtotal * 0.19, 2);
    SET p_total = p_subtotal + p_impuesto;
END$$

-- =====================================================
-- PROCEDIMIENTO: Registrar venta completa
-- =====================================================
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
    DECLARE v_stock_actual INT;
    
    DECLARE EXIT HANDLER FOR SQLEXCEPTION
    BEGIN
        ROLLBACK;
        SIGNAL SQLSTATE '45000'
        SET MESSAGE_TEXT = 'Error al registrar la venta';
    END;
    
    START TRANSACTION;
    
    SET v_array_length = JSON_LENGTH(p_productos);
    IF v_array_length = 0 THEN
        SIGNAL SQLSTATE '45000'
        SET MESSAGE_TEXT = 'Debe incluir al menos un producto';
    END IF;
    
    -- Calcular subtotal
    WHILE v_index < v_array_length DO
        SET v_producto_json = JSON_EXTRACT(p_productos, CONCAT('$[', v_index, ']'));
        SET v_producto_id = JSON_UNQUOTE(JSON_EXTRACT(v_producto_json, '$.producto_id'));
        SET v_cantidad = JSON_UNQUOTE(JSON_EXTRACT(v_producto_json, '$.cantidad'));
        SET v_precio_unitario = JSON_UNQUOTE(JSON_EXTRACT(v_producto_json, '$.precio_unitario'));
        
        -- Validar producto existe y tiene stock
        SELECT cantidad INTO v_stock_actual
        FROM productos 
        WHERE id = v_producto_id AND estado = 'activo';
        
        IF v_stock_actual IS NULL THEN
            SIGNAL SQLSTATE '45000'
            SET MESSAGE_TEXT = 'Producto no existe o esta inactivo';
        END IF;
        
        IF v_stock_actual < v_cantidad THEN
            SIGNAL SQLSTATE '45000'
            SET MESSAGE_TEXT = 'Stock insuficiente';
        END IF;
        
        SET v_subtotal_detalle = v_cantidad * v_precio_unitario;
        SET v_subtotal = v_subtotal + v_subtotal_detalle;
        
        SET v_index = v_index + 1;
    END WHILE;
    
    -- Calcular totales
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
    
    -- Insertar detalles
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
    
    COMMIT;
END$$

DELIMITER ;

-- Mostrar procedimientos creados
SELECT 
    ROUTINE_NAME as procedimiento,
    ROUTINE_TYPE as tipo,
    CREATED as fecha_creacion
FROM information_schema.ROUTINES
WHERE ROUTINE_SCHEMA = 'papeleria_jakake'
  AND ROUTINE_TYPE = 'PROCEDURE'
ORDER BY ROUTINE_NAME;

SELECT '✓ Procedimientos almacenados creados exitosamente' as mensaje;
SELECT COUNT(*) as total_procedimientos 
FROM information_schema.ROUTINES
WHERE ROUTINE_SCHEMA = 'papeleria_jakake'
  AND ROUTINE_TYPE = 'PROCEDURE';
