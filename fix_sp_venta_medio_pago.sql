DELIMITER $$

DROP PROCEDURE IF EXISTS sp_registrar_venta_completa$$

CREATE PROCEDURE sp_registrar_venta_completa(
    IN p_cliente_id INT,
    IN p_usuario_id INT,
    IN p_medio_pago ENUM('efectivo', 'tarjeta', 'transferencia'),
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

    -- Validar que hay productos
    SET v_array_length = JSON_LENGTH(p_productos);
    IF v_array_length = 0 THEN
        SIGNAL SQLSTATE '45000'
        SET MESSAGE_TEXT = 'Debe incluir al menos un producto';
    END IF;

    -- Calcular subtotal y validar stock
    WHILE v_index < v_array_length DO
        SET v_producto_json = JSON_EXTRACT(p_productos, CONCAT('$[', v_index, ']'));
        SET v_producto_id = JSON_UNQUOTE(JSON_EXTRACT(v_producto_json, '$.producto_id'));
        SET v_cantidad = JSON_UNQUOTE(JSON_EXTRACT(v_producto_json, '$.cantidad'));
        SET v_precio_unitario = JSON_UNQUOTE(JSON_EXTRACT(v_producto_json, '$.precio_unitario'));

        -- Verificar stock
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

    -- Calcular impuesto y total
    CALL sp_calcular_totales_venta(v_subtotal, v_impuesto, v_total);

    -- Insertar venta con medio_pago
    INSERT INTO ventas (
        fecha,
        cliente_id,
        usuario_id,
        subtotal,
        impuesto,
        total,
        medio_pago,
        estado
    ) VALUES (
        CURDATE(),
        p_cliente_id,
        p_usuario_id,
        v_subtotal,
        v_impuesto,
        v_total,
        p_medio_pago,
        'completada'
    );

    SET p_venta_id = LAST_INSERT_ID();
    SET p_total_final = v_total;

    -- Insertar detalles y actualizar stock
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

        -- Actualizar stock del producto
        UPDATE productos
        SET cantidad = cantidad - v_cantidad
        WHERE id = v_producto_id;

        SET v_index = v_index + 1;
    END WHILE;

    COMMIT;
END$$

DELIMITER ;
