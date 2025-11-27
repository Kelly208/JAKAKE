-- =====================================================
-- ÍNDICES ADICIONALES Y OPTIMIZACIÓN
-- Sistema de Base de Datos - Papelería JAKAKE
-- =====================================================
-- Descripción: Índices adicionales para optimización
--              (muchos ya fueron creados en 01_crear_tablas.sql)
-- Autor: Persona 1
-- Fecha: 2025-11-27
-- =====================================================

USE papeleria_jakake;

-- =====================================================
-- NOTA: Los siguientes índices YA fueron creados en 01_crear_tablas.sql:
-- - productos.codigo (UNIQUE)
-- - productos.nombre
-- - productos.proveedor_id
-- - ventas.fecha
-- - ventas.cliente_id
-- - ventas.usuario_id
-- - detalle_venta.producto_id
-- - auditoria.fecha_hora
-- - auditoria.tabla_afectada
-- - clientes.cedula (UNIQUE)
-- =====================================================

-- =====================================================
-- ÍNDICES COMPUESTOS ADICIONALES
-- Para mejorar consultas que usan múltiples columnas
-- =====================================================

-- Índice compuesto para búsqueda de ventas por fecha y cliente
CREATE INDEX idx_ventas_fecha_cliente ON ventas(fecha, cliente_id);

-- Índice compuesto para búsqueda de ventas por fecha y estado
CREATE INDEX idx_ventas_fecha_estado ON ventas(fecha, estado);

-- Índice compuesto para auditoría por tabla y fecha
CREATE INDEX idx_auditoria_tabla_fecha ON auditoria(tabla_afectada, fecha_hora);

-- Índice compuesto para bonos por cliente y estado
CREATE INDEX idx_bonos_cliente_estado ON bonos_regalo(cliente_id, estado);

-- Índice compuesto para productos por proveedor y estado
CREATE INDEX idx_productos_proveedor_estado ON productos(proveedor_id, estado);

-- Índice compuesto para movimientos por producto y fecha
CREATE INDEX idx_movimientos_producto_fecha ON movimientos_inventario(producto_id, fecha_movimiento);

-- =====================================================
-- ÍNDICES FULLTEXT PARA BÚSQUEDAS DE TEXTO
-- Permiten búsquedas más eficientes en campos de texto
-- =====================================================

-- Índice fulltext para búsqueda de productos por nombre y descripción
ALTER TABLE productos ADD FULLTEXT idx_productos_texto(nombre, descripcion);

-- Índice fulltext para búsqueda de clientes por nombre
ALTER TABLE clientes ADD FULLTEXT idx_clientes_nombre(nombre);

-- Índice fulltext para búsqueda de proveedores por nombre
ALTER TABLE proveedores ADD FULLTEXT idx_proveedores_nombre(nombre);

-- =====================================================
-- ÍNDICES PARA ORDENAMIENTO FRECUENTE
-- =====================================================

-- Índice para ordenar productos por precio
CREATE INDEX idx_productos_precio ON productos(precio);

-- Índice para ordenar clientes por fecha de registro
CREATE INDEX idx_clientes_fecha_registro ON clientes(fecha_registro);

-- Índice para ordenar ventas por total
CREATE INDEX idx_ventas_total ON ventas(total);

-- =====================================================
-- ANÁLISIS Y ESTADÍSTICAS DE ÍNDICES
-- =====================================================

-- Analizar todas las tablas para optimizar índices
ANALYZE TABLE usuarios;
ANALYZE TABLE clientes;
ANALYZE TABLE proveedores;
ANALYZE TABLE productos;
ANALYZE TABLE ventas;
ANALYZE TABLE detalle_venta;
ANALYZE TABLE devoluciones;
ANALYZE TABLE bonos_regalo;
ANALYZE TABLE movimientos_inventario;
ANALYZE TABLE auditoria;
ANALYZE TABLE politicas_datos;
ANALYZE TABLE aceptacion_politicas;

-- =====================================================
-- VERIFICACIÓN DE ÍNDICES CREADOS
-- =====================================================

-- Ver todos los índices por tabla
SELECT 
    TABLE_NAME AS tabla,
    INDEX_NAME AS indice,
    COLUMN_NAME AS columna,
    SEQ_IN_INDEX AS posicion,
    INDEX_TYPE AS tipo
FROM information_schema.STATISTICS
WHERE TABLE_SCHEMA = 'papeleria_jakake'
ORDER BY TABLE_NAME, INDEX_NAME, SEQ_IN_INDEX;

-- Contar índices por tabla
SELECT 
    TABLE_NAME AS tabla,
    COUNT(DISTINCT INDEX_NAME) AS total_indices
FROM information_schema.STATISTICS
WHERE TABLE_SCHEMA = 'papeleria_jakake'
GROUP BY TABLE_NAME
ORDER BY total_indices DESC;

-- =====================================================
-- RECOMENDACIONES DE USO
-- =====================================================

/*
ÍNDICES FULLTEXT - Ejemplos de uso:

-- Búsqueda de productos:
SELECT * FROM productos 
WHERE MATCH(nombre, descripcion) AGAINST('lapiz rojo' IN NATURAL LANGUAGE MODE);

-- Búsqueda de clientes:
SELECT * FROM clientes 
WHERE MATCH(nombre) AGAINST('juan garcia' IN NATURAL LANGUAGE MODE);

ÍNDICES COMPUESTOS - Casos de uso:

-- Consultas que se benefician del índice idx_ventas_fecha_cliente:
SELECT * FROM ventas WHERE fecha BETWEEN '2025-01-01' AND '2025-12-31' AND cliente_id = 5;

-- Consultas que se benefician del índice idx_ventas_fecha_estado:
SELECT * FROM ventas WHERE fecha >= '2025-01-01' AND estado = 'completada';

MANTENIMIENTO:

-- Ejecutar periódicamente para mantener estadísticas actualizadas:
ANALYZE TABLE nombre_tabla;

-- Verificar uso de índices en consultas:
EXPLAIN SELECT * FROM tabla WHERE condicion;
*/

SELECT 'Índices adicionales creados y tablas analizadas exitosamente' AS mensaje;
SELECT COUNT(DISTINCT INDEX_NAME) AS total_indices_sistema
FROM information_schema.STATISTICS
WHERE TABLE_SCHEMA = 'papeleria_jakake';
