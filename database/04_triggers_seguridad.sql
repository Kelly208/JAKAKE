-- =====================================================
-- TRIGGERS DE SEGURIDAD Y VALIDACIÓN
-- Sistema de Base de Datos - Papelería JAKAKE
-- =====================================================
-- Descripción: Triggers para prevenir operaciones
--              no permitidas y validar datos críticos
-- Autor: Persona 1
-- Fecha: 2025-11-26
-- =====================================================

USE papeleria_jakake;

DELIMITER $$

-- =====================================================
-- TRIGGER: Prevenir eliminación de ventas
-- Se ejecuta BEFORE DELETE en ventas
-- Las ventas NO pueden eliminarse, solo cambiar estado
-- =====================================================

CREATE TRIGGER tr_prevenir_eliminar_ventas
BEFORE DELETE ON ventas
FOR EACH ROW
BEGIN
    SIGNAL SQLSTATE '45000'
    SET MESSAGE_TEXT = 'Error: No se pueden eliminar ventas. Solo puede cambiar el estado a "cancelada"',
        MYSQL_ERRNO = 2001;
END$$

-- =====================================================
-- TRIGGER: Validar stock antes de venta
-- Se ejecuta BEFORE INSERT en detalle_venta
-- Verifica que haya suficiente cantidad del producto
-- =====================================================

CREATE TRIGGER tr_validar_stock_venta
BEFORE INSERT ON detalle_venta
FOR EACH ROW
BEGIN
    DECLARE v_cantidad_disponible INT;
    DECLARE v_nombre_producto VARCHAR(200);
    
    -- Obtener cantidad disponible del producto
    SELECT cantidad, nombre INTO v_cantidad_disponible, v_nombre_producto
    FROM productos
    WHERE id = NEW.producto_id AND estado = 'activo';
    
    -- Validar que el producto existe y está activo
    IF v_cantidad_disponible IS NULL THEN
        SIGNAL SQLSTATE '45000'
        SET MESSAGE_TEXT = 'Error: El producto no existe o está inactivo',
            MYSQL_ERRNO = 2002;
    END IF;
    
    -- Validar que hay suficiente stock
    IF v_cantidad_disponible < NEW.cantidad THEN
        SIGNAL SQLSTATE '45000'
        SET MESSAGE_TEXT = CONCAT('Error: Stock insuficiente para el producto "', 
                                  v_nombre_producto, 
                                  '". Disponible: ', v_cantidad_disponible, 
                                  ', Solicitado: ', NEW.cantidad),
            MYSQL_ERRNO = 2003;
    END IF;
    
    -- Calcular subtotal automáticamente
    SET NEW.subtotal = NEW.cantidad * NEW.precio_unitario;
END$$

-- =====================================================
-- TRIGGER: Validar cantidad en devolución
-- Se ejecuta BEFORE INSERT en devoluciones
-- Verifica que la devolución sea válida
-- =====================================================

CREATE TRIGGER tr_validar_devolucion
BEFORE INSERT ON devoluciones
FOR EACH ROW
BEGIN
    DECLARE v_cantidad_vendida INT;
    DECLARE v_precio_venta DECIMAL(10,2);
    
    -- Obtener cantidad vendida del producto en esa venta
    SELECT cantidad, precio_unitario 
    INTO v_cantidad_vendida, v_precio_venta
    FROM detalle_venta
    WHERE venta_id = NEW.venta_id 
      AND producto_id = NEW.producto_id;
    
    -- Validar que el producto fue vendido en esa venta
    IF v_cantidad_vendida IS NULL THEN
        SIGNAL SQLSTATE '45000'
        SET MESSAGE_TEXT = 'Error: El producto no fue vendido en esta venta',
            MYSQL_ERRNO = 2004;
    END IF;
    
    -- Validar que la cantidad a devolver no sea mayor a la vendida
    IF NEW.cantidad > v_cantidad_vendida THEN
        SIGNAL SQLSTATE '45000'
        SET MESSAGE_TEXT = CONCAT('Error: No se puede devolver más de lo vendido. ',
                                  'Vendido: ', v_cantidad_vendida, 
                                  ', Solicitado devolver: ', NEW.cantidad),
            MYSQL_ERRNO = 2005;
    END IF;
    
    -- Calcular valor de devolución automáticamente
    SET NEW.valor_devolucion = NEW.cantidad * v_precio_venta;
END$$

-- =====================================================
-- TRIGGER: Validar cliente antes de venta
-- Se ejecuta BEFORE INSERT en ventas
-- Asegura que el cliente existe (si se proporciona)
-- =====================================================

CREATE TRIGGER tr_validar_cliente_venta
BEFORE INSERT ON ventas
FOR EACH ROW
BEGIN
    DECLARE v_cliente_existe INT;
    
    -- Si se proporciona cliente_id, verificar que existe
    IF NEW.cliente_id IS NOT NULL THEN
        SELECT COUNT(*) INTO v_cliente_existe
        FROM clientes
        WHERE id = NEW.cliente_id;
        
        IF v_cliente_existe = 0 THEN
            SIGNAL SQLSTATE '45000'
            SET MESSAGE_TEXT = 'Error: El cliente especificado no existe',
                MYSQL_ERRNO = 2006;
        END IF;
    END IF;
    
    -- Validar que el cajero existe y está activo
    IF NOT EXISTS (
        SELECT 1 FROM usuarios 
        WHERE id = NEW.usuario_id 
          AND rol IN ('cajero', 'administrador')
          AND estado = 'activo'
    ) THEN
        SIGNAL SQLSTATE '45000'
        SET MESSAGE_TEXT = 'Error: El usuario no es un cajero válido o no está activo',
            MYSQL_ERRNO = 2007;
    END IF;
END$$

-- =====================================================
-- TRIGGER: Validar email único en usuarios
-- Se ejecuta BEFORE INSERT y BEFORE UPDATE en usuarios
-- =====================================================

CREATE TRIGGER tr_validar_email_usuario_insert
BEFORE INSERT ON usuarios
FOR EACH ROW
BEGIN
    IF EXISTS (SELECT 1 FROM usuarios WHERE email = NEW.email) THEN
        SIGNAL SQLSTATE '45000'
        SET MESSAGE_TEXT = 'Error: El email ya está registrado',
            MYSQL_ERRNO = 2008;
    END IF;
    
    -- Validar formato de email básico
    IF NEW.email NOT LIKE '%@%.%' THEN
        SIGNAL SQLSTATE '45000'
        SET MESSAGE_TEXT = 'Error: El formato del email no es válido',
            MYSQL_ERRNO = 2009;
    END IF;
END$$

CREATE TRIGGER tr_validar_email_usuario_update
BEFORE UPDATE ON usuarios
FOR EACH ROW
BEGIN
    IF NEW.email != OLD.email THEN
        IF EXISTS (SELECT 1 FROM usuarios WHERE email = NEW.email AND id != NEW.id) THEN
            SIGNAL SQLSTATE '45000'
            SET MESSAGE_TEXT = 'Error: El email ya está registrado por otro usuario',
                MYSQL_ERRNO = 2008;
        END IF;
        
        IF NEW.email NOT LIKE '%@%.%' THEN
            SIGNAL SQLSTATE '45000'
            SET MESSAGE_TEXT = 'Error: El formato del email no es válido',
                MYSQL_ERRNO = 2009;
        END IF;
    END IF;
END$$

-- =====================================================
-- TRIGGER: Validar cédula única en clientes
-- Se ejecuta BEFORE INSERT y BEFORE UPDATE en clientes
-- =====================================================

CREATE TRIGGER tr_validar_cedula_cliente_insert
BEFORE INSERT ON clientes
FOR EACH ROW
BEGIN
    IF EXISTS (SELECT 1 FROM clientes WHERE cedula = NEW.cedula) THEN
        SIGNAL SQLSTATE '45000'
        SET MESSAGE_TEXT = 'Error: La cédula ya está registrada',
            MYSQL_ERRNO = 2010;
    END IF;
END$$

CREATE TRIGGER tr_validar_cedula_cliente_update
BEFORE UPDATE ON clientes
FOR EACH ROW
BEGIN
    IF NEW.cedula != OLD.cedula THEN
        IF EXISTS (SELECT 1 FROM clientes WHERE cedula = NEW.cedula AND id != NEW.id) THEN
            SIGNAL SQLSTATE '45000'
            SET MESSAGE_TEXT = 'Error: La cédula ya está registrada por otro cliente',
                MYSQL_ERRNO = 2010;
        END IF;
    END IF;
END$$

-- =====================================================
-- TRIGGER: Validar precios positivos
-- Se ejecuta BEFORE INSERT y BEFORE UPDATE en productos
-- =====================================================

CREATE TRIGGER tr_validar_precio_producto_insert
BEFORE INSERT ON productos
FOR EACH ROW
BEGIN
    IF NEW.precio <= 0 THEN
        SIGNAL SQLSTATE '45000'
        SET MESSAGE_TEXT = 'Error: El precio del producto debe ser mayor a cero',
            MYSQL_ERRNO = 2011;
    END IF;
END$$

CREATE TRIGGER tr_validar_precio_producto_update
BEFORE UPDATE ON productos
FOR EACH ROW
BEGIN
    IF NEW.precio <= 0 THEN
        SIGNAL SQLSTATE '45000'
        SET MESSAGE_TEXT = 'Error: El precio del producto debe ser mayor a cero',
            MYSQL_ERRNO = 2011;
    END IF;
END$$

-- =====================================================
-- TRIGGER: Validar totales de venta
-- Se ejecuta BEFORE INSERT en ventas
-- Asegura consistencia en cálculos
-- =====================================================

CREATE TRIGGER tr_validar_totales_venta
BEFORE INSERT ON ventas
FOR EACH ROW
BEGIN
    -- Validar que subtotal, impuesto y total sean consistentes
    IF ABS(NEW.total - (NEW.subtotal + NEW.impuesto)) > 0.01 THEN
        SIGNAL SQLSTATE '45000'
        SET MESSAGE_TEXT = 'Error: Los totales de la venta no son consistentes',
            MYSQL_ERRNO = 2012;
    END IF;
    
    -- Validar que el impuesto sea aproximadamente 19% del subtotal
    IF ABS(NEW.impuesto - (NEW.subtotal * 0.19)) > 1 THEN
        SIGNAL SQLSTATE '45000'
        SET MESSAGE_TEXT = 'Error: El cálculo del impuesto (IVA 19%) no es correcto',
            MYSQL_ERRNO = 2013;
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
    ACTION_TIMING
FROM information_schema.TRIGGERS
WHERE TRIGGER_SCHEMA = 'papeleria_jakake'
  AND (TRIGGER_NAME LIKE 'tr_prevenir%' 
       OR TRIGGER_NAME LIKE 'tr_validar%')
ORDER BY EVENT_OBJECT_TABLE, ACTION_TIMING, EVENT_MANIPULATION;

SELECT 'Triggers de seguridad y validación creados exitosamente' AS mensaje;
SELECT 'Total de triggers activos en el sistema:' AS info, 
       COUNT(*) AS total_triggers
FROM information_schema.TRIGGERS
WHERE TRIGGER_SCHEMA = 'papeleria_jakake';
