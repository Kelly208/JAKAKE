-- =====================================================
-- FUNCIONES SQL
-- Sistema de Base de Datos - Papelería JAKAKE
-- =====================================================
-- Descripción: Funciones para cálculos y consultas
--              que retornan valores únicos
-- Autor: Persona 1
-- Fecha: 2025-11-27
-- =====================================================

USE papeleria_jakake;

DELIMITER $$

-- =====================================================
-- FUNCIÓN: Calcular IVA
-- Calcula el IVA (19%) sobre un subtotal
-- =====================================================

DROP FUNCTION IF EXISTS fn_calcular_iva$$

CREATE FUNCTION fn_calcular_iva(
    p_subtotal DECIMAL(10,2)
)
RETURNS DECIMAL(10,2)
DETERMINISTIC
BEGIN
    RETURN ROUND(p_subtotal * 0.19, 2);
END$$

-- =====================================================
-- FUNCIÓN: Calcular edad de cliente
-- Retorna la edad en años a partir de fecha de nacimiento
-- =====================================================

DROP FUNCTION IF EXISTS fn_edad_cliente$$

CREATE FUNCTION fn_edad_cliente(
    p_fecha_nacimiento DATE
)
RETURNS INT
DETERMINISTIC
BEGIN
    RETURN TIMESTAMPDIFF(YEAR, p_fecha_nacimiento, CURDATE());
END$$

-- =====================================================
-- FUNCIÓN: Stock disponible de producto
-- Retorna la cantidad actual disponible
-- =====================================================

DROP FUNCTION IF EXISTS fn_stock_disponible$$

CREATE FUNCTION fn_stock_disponible(
    p_producto_id INT
)
RETURNS INT
READS SQL DATA
BEGIN
    DECLARE v_cantidad INT;
    
    SELECT cantidad INTO v_cantidad
    FROM productos
    WHERE id = p_producto_id AND estado = 'activo';
    
    RETURN COALESCE(v_cantidad, 0);
END$$

-- =====================================================
-- FUNCIÓN: Total de ventas de un cliente
-- Retorna el total gastado por un cliente
-- =====================================================

DROP FUNCTION IF EXISTS fn_total_ventas_cliente$$

CREATE FUNCTION fn_total_ventas_cliente(
    p_cliente_id INT
)
RETURNS DECIMAL(10,2)
READS SQL DATA
BEGIN
    DECLARE v_total DECIMAL(10,2);
    
    SELECT COALESCE(SUM(total), 0) INTO v_total
    FROM ventas
    WHERE cliente_id = p_cliente_id AND estado = 'completada';
    
    RETURN v_total;
END$$

-- =====================================================
-- FUNCIÓN: Validar stock suficiente
-- Verifica si hay stock suficiente de un producto
-- Retorna 1 si hay stock, 0 si no hay
-- =====================================================

DROP FUNCTION IF EXISTS fn_validar_stock_suficiente$$

CREATE FUNCTION fn_validar_stock_suficiente(
    p_producto_id INT,
    p_cantidad_requerida INT
)
RETURNS TINYINT(1)
READS SQL DATA
BEGIN
    DECLARE v_stock_actual INT;
    
    SELECT cantidad INTO v_stock_actual
    FROM productos
    WHERE id = p_producto_id AND estado = 'activo';
    
    IF v_stock_actual IS NULL THEN
        RETURN 0;
    END IF;
    
    IF v_stock_actual >= p_cantidad_requerida THEN
        RETURN 1;
    ELSE
        RETURN 0;
    END IF;
END$$

-- =====================================================
-- FUNCIÓN: Obtener nombre de producto
-- Retorna el nombre del producto dado su ID
-- =====================================================

DROP FUNCTION IF EXISTS fn_nombre_producto$$

CREATE FUNCTION fn_nombre_producto(
    p_producto_id INT
)
RETURNS VARCHAR(200)
READS SQL DATA
BEGIN
    DECLARE v_nombre VARCHAR(200);
    
    SELECT nombre INTO v_nombre
    FROM productos
    WHERE id = p_producto_id;
    
    RETURN COALESCE(v_nombre, 'Producto no encontrado');
END$$

-- =====================================================
-- FUNCIÓN: Calcular total con descuento
-- Aplica un porcentaje de descuento a un monto
-- =====================================================

DROP FUNCTION IF EXISTS fn_aplicar_descuento$$

CREATE FUNCTION fn_aplicar_descuento(
    p_monto DECIMAL(10,2),
    p_porcentaje_descuento DECIMAL(5,2)
)
RETURNS DECIMAL(10,2)
DETERMINISTIC
BEGIN
    DECLARE v_descuento DECIMAL(10,2);
    DECLARE v_total_final DECIMAL(10,2);
    
    SET v_descuento = p_monto * (p_porcentaje_descuento / 100);
    SET v_total_final = p_monto - v_descuento;
    
    RETURN ROUND(v_total_final, 2);
END$$

-- =====================================================
-- FUNCIÓN: Contar compras de cliente
-- Retorna el número de compras realizadas por un cliente
-- =====================================================

DROP FUNCTION IF EXISTS fn_contar_compras_cliente$$

CREATE FUNCTION fn_contar_compras_cliente(
    p_cliente_id INT
)
RETURNS INT
READS SQL DATA
BEGIN
    DECLARE v_total INT;
    
    SELECT COUNT(*) INTO v_total
    FROM ventas
    WHERE cliente_id = p_cliente_id AND estado = 'completada';
    
    RETURN v_total;
END$$

-- =====================================================
-- FUNCIÓN: Verificar bono activo
-- Verifica si un bono está activo y vigente
-- Retorna 1 si es válido, 0 si no
-- =====================================================

DROP FUNCTION IF EXISTS fn_verificar_bono_activo$$

CREATE FUNCTION fn_verificar_bono_activo(
    p_codigo_bono VARCHAR(50)
)
RETURNS TINYINT(1)
READS SQL DATA
BEGIN
    DECLARE v_valido TINYINT(1);
    
    SELECT COUNT(*) INTO v_valido
    FROM bonos_regalo
    WHERE codigo = p_codigo_bono
      AND estado = 'activo'
      AND fecha_vencimiento >= CURDATE();
    
    IF v_valido > 0 THEN
        RETURN 1;
    ELSE
        RETURN 0;
    END IF;
END$$

-- =====================================================
-- FUNCIÓN: Valor total de bonos activos de cliente
-- Retorna la suma de todos los bonos activos
-- =====================================================

DROP FUNCTION IF EXISTS fn_valor_bonos_cliente$$

CREATE FUNCTION fn_valor_bonos_cliente(
    p_cliente_id INT
)
RETURNS DECIMAL(10,2)
READS SQL DATA
BEGIN
    DECLARE v_valor_total DECIMAL(10,2);
    
    SELECT COALESCE(SUM(valor), 0) INTO v_valor_total
    FROM bonos_regalo
    WHERE cliente_id = p_cliente_id
      AND estado = 'activo'
      AND fecha_vencimiento >= CURDATE();
    
    RETURN v_valor_total;
END$$

-- =====================================================
-- FUNCIÓN: Calcular margen de ganancia
-- Calcula el margen entre precio de venta y costo
-- =====================================================

DROP FUNCTION IF EXISTS fn_calcular_margen$$

CREATE FUNCTION fn_calcular_margen(
    p_precio_venta DECIMAL(10,2),
    p_precio_costo DECIMAL(10,2)
)
RETURNS DECIMAL(5,2)
DETERMINISTIC
BEGIN
    DECLARE v_margen DECIMAL(5,2);
    
    IF p_precio_costo = 0 THEN
        RETURN 0;
    END IF;
    
    SET v_margen = ((p_precio_venta - p_precio_costo) / p_precio_costo) * 100;
    
    RETURN ROUND(v_margen, 2);
END$$

-- =====================================================
-- FUNCIÓN: Promedio de venta por cliente
-- Retorna el ticket promedio de un cliente
-- =====================================================

DROP FUNCTION IF EXISTS fn_promedio_venta_cliente$$

CREATE FUNCTION fn_promedio_venta_cliente(
    p_cliente_id INT
)
RETURNS DECIMAL(10,2)
READS SQL DATA
BEGIN
    DECLARE v_promedio DECIMAL(10,2);
    
    SELECT COALESCE(AVG(total), 0) INTO v_promedio
    FROM ventas
    WHERE cliente_id = p_cliente_id AND estado = 'completada';
    
    RETURN ROUND(v_promedio, 2);
END$$

-- =====================================================
-- FUNCIÓN: Días desde última compra
-- Retorna cuántos días han pasado desde la última compra
-- =====================================================

DROP FUNCTION IF EXISTS fn_dias_ultima_compra$$

CREATE FUNCTION fn_dias_ultima_compra(
    p_cliente_id INT
)
RETURNS INT
READS SQL DATA
BEGIN
    DECLARE v_ultima_fecha DATE;
    DECLARE v_dias INT;
    
    SELECT MAX(fecha) INTO v_ultima_fecha
    FROM ventas
    WHERE cliente_id = p_cliente_id AND estado = 'completada';
    
    IF v_ultima_fecha IS NULL THEN
        RETURN -1; -- Cliente sin compras
    END IF;
    
    SET v_dias = DATEDIFF(CURDATE(), v_ultima_fecha);
    
    RETURN v_dias;
END$$

-- =====================================================
-- FUNCIÓN: Validar proveedor activo
-- Verifica si un proveedor está activo
-- =====================================================

DROP FUNCTION IF EXISTS fn_proveedor_activo$$

CREATE FUNCTION fn_proveedor_activo(
    p_proveedor_id INT
)
RETURNS TINYINT(1)
READS SQL DATA
BEGIN
    DECLARE v_activo TINYINT(1);
    
    SELECT COUNT(*) INTO v_activo
    FROM proveedores
    WHERE id = p_proveedor_id AND estado = 'activo';
    
    RETURN v_activo;
END$$

-- =====================================================
-- FUNCIÓN: Calcular rotación de inventario
-- Días promedio que tarda en venderse un producto
-- =====================================================

DROP FUNCTION IF EXISTS fn_rotacion_producto$$

CREATE FUNCTION fn_rotacion_producto(
    p_producto_id INT,
    p_dias_analisis INT
)
RETURNS DECIMAL(10,2)
READS SQL DATA
BEGIN
    DECLARE v_cantidad_vendida INT;
    DECLARE v_stock_promedio DECIMAL(10,2);
    DECLARE v_rotacion DECIMAL(10,2);
    
    -- Cantidad vendida en el período
    SELECT COALESCE(SUM(dv.cantidad), 0) INTO v_cantidad_vendida
    FROM detalle_venta dv
    INNER JOIN ventas v ON dv.venta_id = v.id
    WHERE dv.producto_id = p_producto_id
      AND v.fecha >= DATE_SUB(CURDATE(), INTERVAL p_dias_analisis DAY)
      AND v.estado = 'completada';
    
    -- Stock actual como aproximación del stock promedio
    SELECT cantidad INTO v_stock_promedio
    FROM productos
    WHERE id = p_producto_id;
    
    IF v_cantidad_vendida = 0 OR v_stock_promedio = 0 THEN
        RETURN 0;
    END IF;
    
    -- Rotación = (Stock Promedio / Ventas Totales) * Días
    SET v_rotacion = (v_stock_promedio / v_cantidad_vendida) * p_dias_analisis;
    
    RETURN ROUND(v_rotacion, 2);
END$$

DELIMITER ;

-- =====================================================
-- VERIFICACIÓN DE FUNCIONES CREADAS
-- =====================================================

SELECT 
    ROUTINE_NAME AS funcion,
    ROUTINE_TYPE AS tipo,
    DATA_TYPE AS tipo_retorno,
    CREATED AS fecha_creacion
FROM information_schema.ROUTINES
WHERE ROUTINE_SCHEMA = 'papeleria_jakake'
  AND ROUTINE_TYPE = 'FUNCTION'
ORDER BY ROUTINE_NAME;

SELECT 'Funciones SQL creadas exitosamente' AS mensaje;
SELECT COUNT(*) AS total_funciones
FROM information_schema.ROUTINES
WHERE ROUTINE_SCHEMA = 'papeleria_jakake'
  AND ROUTINE_TYPE = 'FUNCTION';

-- =====================================================
-- EJEMPLOS DE USO DE FUNCIONES
-- =====================================================

-- Descomentar para probar:
/*
-- Calcular IVA de $10000
SELECT fn_calcular_iva(10000) AS iva;

-- Ver edad de un cliente (requiere datos)
SELECT nombre, fecha_nacimiento, fn_edad_cliente(fecha_nacimiento) AS edad
FROM clientes
LIMIT 5;

-- Stock disponible de productos
SELECT id, nombre, fn_stock_disponible(id) AS stock
FROM productos
LIMIT 5;
*/
