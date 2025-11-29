-- Procedimiento para procesar devoluciones
DROP PROCEDURE IF EXISTS sp_procesar_devolucion;

CREATE PROCEDURE sp_procesar_devolucion(
    IN p_venta_id INT,
    IN p_usuario_id INT,
    IN p_motivo TEXT,
    IN p_productos JSON
)
BEGIN
    DECLARE v_devolucion_id INT;
    DECLARE v_monto_devolucion DECIMAL(10,2) DEFAULT 0;
    DECLARE v_codigo_bono VARCHAR(20);
    DECLARE v_idx INT DEFAULT 0;
    DECLARE v_count INT;
    DECLARE v_producto_id INT;
    DECLARE v_cantidad INT;
    DECLARE v_precio_unitario DECIMAL(10,2);
    DECLARE v_subtotal DECIMAL(10,2);
    DECLARE v_cliente_id INT;
    
    DECLARE EXIT HANDLER FOR SQLEXCEPTION
    BEGIN
        ROLLBACK;
        RESIGNAL;
    END;
    
    START TRANSACTION;
    
    -- Obtener cliente_id de la venta
    SELECT cliente_id INTO v_cliente_id
    FROM ventas
    WHERE id = p_venta_id;
    
    -- Obtener cantidad de productos
    SET v_count = JSON_LENGTH(p_productos);
    
    -- Procesar cada producto devuelto
    WHILE v_idx < v_count DO
        SET v_producto_id = JSON_UNQUOTE(JSON_EXTRACT(p_productos, CONCAT('$[', v_idx, '].producto_id')));
        SET v_cantidad = JSON_UNQUOTE(JSON_EXTRACT(p_productos, CONCAT('$[', v_idx, '].cantidad')));
        
        -- Obtener precio del detalle de venta
        SELECT precio_unitario INTO v_precio_unitario
        FROM detalle_venta
        WHERE venta_id = p_venta_id AND producto_id = v_producto_id
        LIMIT 1;
        
        SET v_subtotal = v_precio_unitario * v_cantidad;
        SET v_monto_devolucion = v_monto_devolucion + v_subtotal;
        
        -- Insertar una fila de devolución por cada producto
        INSERT INTO devoluciones (venta_id, producto_id, cantidad, valor_devolucion, motivo, usuario_id, fecha_devolucion)
        VALUES (p_venta_id, v_producto_id, v_cantidad, v_subtotal, p_motivo, p_usuario_id, CURDATE());
        
        -- Guardar el primer ID para el bono
        IF v_idx = 0 THEN
            SET v_devolucion_id = LAST_INSERT_ID();
        END IF;
        
        -- Devolver stock del producto
        UPDATE productos 
        SET cantidad = cantidad + v_cantidad
        WHERE id = v_producto_id;
        
        SET v_idx = v_idx + 1;
    END WHILE;
    
    -- Generar código de bono
    SET v_codigo_bono = CONCAT('BONO-', LPAD(v_devolucion_id, 6, '0'));
    
    -- Crear bono de regalo (tabla usa 'valor' no 'monto', y necesita devolucion_id y cliente_id)
    INSERT INTO bonos_regalo (codigo, devolucion_id, cliente_id, valor, estado, fecha_emision, fecha_vencimiento)
    VALUES (v_codigo_bono, v_devolucion_id, v_cliente_id, v_monto_devolucion, 'activo', CURDATE(), DATE_ADD(CURDATE(), INTERVAL 6 MONTH));
    
    COMMIT;
    
    -- Retornar resultados
    SELECT v_devolucion_id as devolucion_id, v_codigo_bono as codigo_bono;
END;
