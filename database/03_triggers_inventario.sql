-- =====================================================
-- TRIGGERS DE INVENTARIO
-- Sistema de Base de Datos - Papelería JAKAKE
-- =====================================================
-- Descripción: Triggers para actualización automática
--              de inventario y registro de movimientos
-- Autor: Persona 1
-- Fecha: 2025-11-26
-- =====================================================

USE papeleria_jakake;

DELIMITER $$

-- =====================================================
-- TRIGGER: Actualizar stock al registrar venta
-- Se ejecuta AFTER INSERT en detalle_venta
-- Disminuye la cantidad del producto vendido
-- =====================================================

CREATE TRIGGER tr_actualizar_stock_venta
AFTER INSERT ON detalle_venta
FOR EACH ROW
BEGIN
    DECLARE v_cantidad_actual INT;
    
    -- Obtener cantidad actual del producto
    SELECT cantidad INTO v_cantidad_actual
    FROM productos
    WHERE id = NEW.producto_id;
    
    -- Actualizar cantidad (disminuir por la venta)
    UPDATE productos
    SET cantidad = cantidad - NEW.cantidad
    WHERE id = NEW.producto_id;
    
    -- Registrar movimiento de inventario
    INSERT INTO movimientos_inventario (
        producto_id,
        tipo_movimiento,
        cantidad_anterior,
        cantidad_nueva,
        cantidad_movimiento,
        motivo,
        usuario_id,
        fecha_movimiento
    )
    SELECT
        NEW.producto_id,
        'salida',
        v_cantidad_actual,
        v_cantidad_actual - NEW.cantidad,
        -NEW.cantidad,
        CONCAT('Venta ID: ', (SELECT venta_id FROM detalle_venta WHERE id = NEW.id)),
        v.usuario_id,
        NOW()
    FROM ventas v
    WHERE v.id = NEW.venta_id;
END$$

-- =====================================================
-- TRIGGER: Actualizar stock al registrar devolución
-- Se ejecuta AFTER INSERT en devoluciones
-- Aumenta la cantidad del producto devuelto
-- =====================================================

CREATE TRIGGER tr_actualizar_stock_devolucion
AFTER INSERT ON devoluciones
FOR EACH ROW
BEGIN
    DECLARE v_cantidad_actual INT;
    
    -- Obtener cantidad actual del producto
    SELECT cantidad INTO v_cantidad_actual
    FROM productos
    WHERE id = NEW.producto_id;
    
    -- Actualizar cantidad (aumentar por la devolución)
    UPDATE productos
    SET cantidad = cantidad + NEW.cantidad
    WHERE id = NEW.producto_id;
    
    -- Registrar movimiento de inventario
    INSERT INTO movimientos_inventario (
        producto_id,
        tipo_movimiento,
        cantidad_anterior,
        cantidad_nueva,
        cantidad_movimiento,
        motivo,
        usuario_id,
        fecha_movimiento
    ) VALUES (
        NEW.producto_id,
        'entrada',
        v_cantidad_actual,
        v_cantidad_actual + NEW.cantidad,
        NEW.cantidad,
        CONCAT('Devolución ID: ', NEW.id, ' - Motivo: ', SUBSTRING(NEW.motivo, 1, 50)),
        NEW.usuario_id,
        NOW()
    );
END$$

-- =====================================================
-- TRIGGER: Registrar ajustes manuales de inventario
-- Se ejecuta AFTER UPDATE en productos
-- Solo registra cuando cambia la cantidad
-- =====================================================

CREATE TRIGGER tr_registrar_movimiento_inventario
AFTER UPDATE ON productos
FOR EACH ROW
BEGIN
    DECLARE v_diferencia INT;
    DECLARE v_tipo_mov VARCHAR(10);
    
    -- Solo ejecutar si cambió la cantidad
    IF OLD.cantidad != NEW.cantidad THEN
        SET v_diferencia = NEW.cantidad - OLD.cantidad;
        
        -- Determinar tipo de movimiento
        IF v_diferencia > 0 THEN
            SET v_tipo_mov = 'entrada';
        ELSEIF v_diferencia < 0 THEN
            SET v_tipo_mov = 'salida';
        ELSE
            SET v_tipo_mov = 'ajuste';
        END IF;
        
        -- Registrar movimiento solo si no fue causado por venta o devolución
        -- (para evitar duplicados, ya que los triggers anteriores ya registran)
        -- Este trigger captura ajustes manuales
        INSERT INTO movimientos_inventario (
            producto_id,
            tipo_movimiento,
            cantidad_anterior,
            cantidad_nueva,
            cantidad_movimiento,
            motivo,
            usuario_id,
            fecha_movimiento
        ) VALUES (
            NEW.id,
            v_tipo_mov,
            OLD.cantidad,
            NEW.cantidad,
            v_diferencia,
            'Ajuste manual de inventario',
            NULL, -- NULL porque puede ser ajuste directo en BD
            NOW()
        );
    END IF;
END$$

-- =====================================================
-- TRIGGER: Validar cantidad no negativa
-- Se ejecuta BEFORE UPDATE en productos
-- Previene que la cantidad sea menor a 0
-- =====================================================

CREATE TRIGGER tr_validar_cantidad_positiva
BEFORE UPDATE ON productos
FOR EACH ROW
BEGIN
    IF NEW.cantidad < 0 THEN
        SIGNAL SQLSTATE '45000'
        SET MESSAGE_TEXT = 'Error: La cantidad del producto no puede ser negativa',
            MYSQL_ERRNO = 1001;
    END IF;
END$$

-- =====================================================
-- TRIGGER: Alerta de stock bajo
-- Se ejecuta AFTER UPDATE en productos
-- Registra en auditoria cuando el stock está bajo
-- =====================================================

CREATE TRIGGER tr_alerta_stock_bajo
AFTER UPDATE ON productos
FOR EACH ROW
BEGIN
    -- Si la cantidad nueva está por debajo del mínimo
    IF NEW.cantidad <= NEW.cantidad_minima AND NEW.cantidad < OLD.cantidad THEN
        INSERT INTO auditoria (
            tabla_afectada,
            operacion,
            registro_id,
            usuario_id,
            valores_anteriores,
            valores_nuevos,
            fecha_hora
        ) VALUES (
            'productos',
            'UPDATE',
            NEW.id,
            NULL,
            JSON_OBJECT(
                'alerta', 'STOCK BAJO',
                'producto', NEW.nombre,
                'cantidad_actual', NEW.cantidad,
                'cantidad_minima', NEW.cantidad_minima
            ),
            JSON_OBJECT(
                'mensaje', 'El producto está por debajo del stock mínimo',
                'accion_sugerida', 'Realizar pedido al proveedor'
            ),
            NOW()
        );
    END IF;
END$$

DELIMITER ;

-- =====================================================
-- VERIFICACIÓN DE TRIGGERS CREADOS
-- =====================================================

SELECT 
    TRIGGER_NAME,
    EVENT_MANIPULATION,
    EVENT_OBJECT_TABLE,
    ACTION_TIMING,
    ACTION_STATEMENT
FROM information_schema.TRIGGERS
WHERE TRIGGER_SCHEMA = 'papeleria_jakake'
  AND (TRIGGER_NAME LIKE 'tr_actualizar%' 
       OR TRIGGER_NAME LIKE 'tr_registrar%'
       OR TRIGGER_NAME LIKE 'tr_validar%'
       OR TRIGGER_NAME LIKE 'tr_alerta%')
ORDER BY EVENT_OBJECT_TABLE, ACTION_TIMING;

SELECT 'Triggers de inventario creados exitosamente' AS mensaje;
