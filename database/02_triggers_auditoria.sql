-- =====================================================
-- TRIGGERS DE AUDITORÍA
-- Sistema de Base de Datos - Papelería JAKAKE
-- =====================================================
-- Descripción: Triggers para registrar automáticamente
--              todas las operaciones CRUD en la tabla
--              auditoria
-- Autor: Persona 1
-- Fecha: 2025-11-26
-- =====================================================

USE papeleria_jakake;

DELIMITER $$

-- =====================================================
-- TRIGGERS PARA TABLA: usuarios
-- =====================================================

-- Trigger AFTER INSERT en usuarios
CREATE TRIGGER tr_auditoria_usuarios_insert
AFTER INSERT ON usuarios
FOR EACH ROW
BEGIN
    INSERT INTO auditoria (
        tabla_afectada,
        operacion,
        registro_id,
        usuario_id,
        valores_anteriores,
        valores_nuevos,
        fecha_hora
    ) VALUES (
        'usuarios',
        'INSERT',
        NEW.id,
        NEW.id, -- El mismo usuario que se creó
        NULL,
        JSON_OBJECT(
            'id', NEW.id,
            'nombre', NEW.nombre,
            'email', NEW.email,
            'rol', NEW.rol,
            'estado', NEW.estado
        ),
        NOW()
    );
END$$

-- Trigger AFTER UPDATE en usuarios
CREATE TRIGGER tr_auditoria_usuarios_update
AFTER UPDATE ON usuarios
FOR EACH ROW
BEGIN
    INSERT INTO auditoria (
        tabla_afectada,
        operacion,
        registro_id,
        usuario_id,
        valores_anteriores,
        valores_nuevos,
        fecha_hora
    ) VALUES (
        'usuarios',
        'UPDATE',
        NEW.id,
        NEW.id,
        JSON_OBJECT(
            'id', OLD.id,
            'nombre', OLD.nombre,
            'email', OLD.email,
            'rol', OLD.rol,
            'estado', OLD.estado,
            'telefono', OLD.telefono,
            'direccion', OLD.direccion
        ),
        JSON_OBJECT(
            'id', NEW.id,
            'nombre', NEW.nombre,
            'email', NEW.email,
            'rol', NEW.rol,
            'estado', NEW.estado,
            'telefono', NEW.telefono,
            'direccion', NEW.direccion
        ),
        NOW()
    );
END$$

-- Trigger AFTER DELETE en usuarios
CREATE TRIGGER tr_auditoria_usuarios_delete
AFTER DELETE ON usuarios
FOR EACH ROW
BEGIN
    INSERT INTO auditoria (
        tabla_afectada,
        operacion,
        registro_id,
        usuario_id,
        valores_anteriores,
        valores_nuevos,
        fecha_hora
    ) VALUES (
        'usuarios',
        'DELETE',
        OLD.id,
        OLD.id,
        JSON_OBJECT(
            'id', OLD.id,
            'nombre', OLD.nombre,
            'email', OLD.email,
            'rol', OLD.rol,
            'estado', OLD.estado
        ),
        NULL,
        NOW()
    );
END$$

-- =====================================================
-- TRIGGERS PARA TABLA: clientes
-- =====================================================

CREATE TRIGGER tr_auditoria_clientes_insert
AFTER INSERT ON clientes
FOR EACH ROW
BEGIN
    INSERT INTO auditoria (
        tabla_afectada,
        operacion,
        registro_id,
        usuario_id,
        valores_anteriores,
        valores_nuevos,
        fecha_hora
    ) VALUES (
        'clientes',
        'INSERT',
        NEW.id,
        NULL, -- Clientes no son usuarios del sistema
        NULL,
        JSON_OBJECT(
            'id', NEW.id,
            'nombre', NEW.nombre,
            'cedula', NEW.cedula,
            'email', NEW.email,
            'telefono', NEW.telefono
        ),
        NOW()
    );
END$$

CREATE TRIGGER tr_auditoria_clientes_update
AFTER UPDATE ON clientes
FOR EACH ROW
BEGIN
    INSERT INTO auditoria (
        tabla_afectada,
        operacion,
        registro_id,
        usuario_id,
        valores_anteriores,
        valores_nuevos,
        fecha_hora
    ) VALUES (
        'clientes',
        'UPDATE',
        NEW.id,
        NULL,
        JSON_OBJECT(
            'id', OLD.id,
            'nombre', OLD.nombre,
            'cedula', OLD.cedula,
            'email', OLD.email,
            'telefono', OLD.telefono,
            'direccion', OLD.direccion
        ),
        JSON_OBJECT(
            'id', NEW.id,
            'nombre', NEW.nombre,
            'cedula', NEW.cedula,
            'email', NEW.email,
            'telefono', NEW.telefono,
            'direccion', NEW.direccion
        ),
        NOW()
    );
END$$

CREATE TRIGGER tr_auditoria_clientes_delete
AFTER DELETE ON clientes
FOR EACH ROW
BEGIN
    INSERT INTO auditoria (
        tabla_afectada,
        operacion,
        registro_id,
        usuario_id,
        valores_anteriores,
        valores_nuevos,
        fecha_hora
    ) VALUES (
        'clientes',
        'DELETE',
        OLD.id,
        NULL,
        JSON_OBJECT(
            'id', OLD.id,
            'nombre', OLD.nombre,
            'cedula', OLD.cedula,
            'email', OLD.email
        ),
        NULL,
        NOW()
    );
END$$

-- =====================================================
-- TRIGGERS PARA TABLA: proveedores
-- =====================================================

CREATE TRIGGER tr_auditoria_proveedores_insert
AFTER INSERT ON proveedores
FOR EACH ROW
BEGIN
    INSERT INTO auditoria (
        tabla_afectada,
        operacion,
        registro_id,
        usuario_id,
        valores_anteriores,
        valores_nuevos,
        fecha_hora
    ) VALUES (
        'proveedores',
        'INSERT',
        NEW.id,
        NULL,
        NULL,
        JSON_OBJECT(
            'id', NEW.id,
            'nombre', NEW.nombre,
            'nit', NEW.nit,
            'ciudad', NEW.ciudad,
            'telefono', NEW.telefono,
            'estado', NEW.estado
        ),
        NOW()
    );
END$$

CREATE TRIGGER tr_auditoria_proveedores_update
AFTER UPDATE ON proveedores
FOR EACH ROW
BEGIN
    INSERT INTO auditoria (
        tabla_afectada,
        operacion,
        registro_id,
        usuario_id,
        valores_anteriores,
        valores_nuevos,
        fecha_hora
    ) VALUES (
        'proveedores',
        'UPDATE',
        NEW.id,
        NULL,
        JSON_OBJECT(
            'id', OLD.id,
            'nombre', OLD.nombre,
            'nit', OLD.nit,
            'ciudad', OLD.ciudad,
            'telefono', OLD.telefono,
            'estado', OLD.estado
        ),
        JSON_OBJECT(
            'id', NEW.id,
            'nombre', NEW.nombre,
            'nit', NEW.nit,
            'ciudad', NEW.ciudad,
            'telefono', NEW.telefono,
            'estado', NEW.estado
        ),
        NOW()
    );
END$$

CREATE TRIGGER tr_auditoria_proveedores_delete
AFTER DELETE ON proveedores
FOR EACH ROW
BEGIN
    INSERT INTO auditoria (
        tabla_afectada,
        operacion,
        registro_id,
        usuario_id,
        valores_anteriores,
        valores_nuevos,
        fecha_hora
    ) VALUES (
        'proveedores',
        'DELETE',
        OLD.id,
        NULL,
        JSON_OBJECT(
            'id', OLD.id,
            'nombre', OLD.nombre,
            'nit', OLD.nit,
            'estado', OLD.estado
        ),
        NULL,
        NOW()
    );
END$$

-- =====================================================
-- TRIGGERS PARA TABLA: productos
-- =====================================================

CREATE TRIGGER tr_auditoria_productos_insert
AFTER INSERT ON productos
FOR EACH ROW
BEGIN
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
        'INSERT',
        NEW.id,
        NULL,
        NULL,
        JSON_OBJECT(
            'id', NEW.id,
            'codigo', NEW.codigo,
            'nombre', NEW.nombre,
            'precio', NEW.precio,
            'cantidad', NEW.cantidad,
            'proveedor_id', NEW.proveedor_id,
            'tipo', NEW.tipo,
            'estado', NEW.estado
        ),
        NOW()
    );
END$$

CREATE TRIGGER tr_auditoria_productos_update
AFTER UPDATE ON productos
FOR EACH ROW
BEGIN
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
            'id', OLD.id,
            'codigo', OLD.codigo,
            'nombre', OLD.nombre,
            'precio', OLD.precio,
            'cantidad', OLD.cantidad,
            'proveedor_id', OLD.proveedor_id,
            'tipo', OLD.tipo,
            'estado', OLD.estado
        ),
        JSON_OBJECT(
            'id', NEW.id,
            'codigo', NEW.codigo,
            'nombre', NEW.nombre,
            'precio', NEW.precio,
            'cantidad', NEW.cantidad,
            'proveedor_id', NEW.proveedor_id,
            'tipo', NEW.tipo,
            'estado', NEW.estado
        ),
        NOW()
    );
END$$

CREATE TRIGGER tr_auditoria_productos_delete
AFTER DELETE ON productos
FOR EACH ROW
BEGIN
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
        'DELETE',
        OLD.id,
        NULL,
        JSON_OBJECT(
            'id', OLD.id,
            'codigo', OLD.codigo,
            'nombre', OLD.nombre,
            'precio', OLD.precio,
            'cantidad', OLD.cantidad,
            'estado', OLD.estado
        ),
        NULL,
        NOW()
    );
END$$

-- =====================================================
-- TRIGGERS PARA TABLA: ventas
-- =====================================================

CREATE TRIGGER tr_auditoria_ventas_insert
AFTER INSERT ON ventas
FOR EACH ROW
BEGIN
    INSERT INTO auditoria (
        tabla_afectada,
        operacion,
        registro_id,
        usuario_id,
        valores_anteriores,
        valores_nuevos,
        fecha_hora
    ) VALUES (
        'ventas',
        'INSERT',
        NEW.id,
        NEW.usuario_id,
        NULL,
        JSON_OBJECT(
            'id', NEW.id,
            'fecha', NEW.fecha,
            'cliente_id', NEW.cliente_id,
            'usuario_id', NEW.usuario_id,
            'subtotal', NEW.subtotal,
            'impuesto', NEW.impuesto,
            'total', NEW.total,
            'estado', NEW.estado
        ),
        NOW()
    );
END$$

CREATE TRIGGER tr_auditoria_ventas_update
AFTER UPDATE ON ventas
FOR EACH ROW
BEGIN
    INSERT INTO auditoria (
        tabla_afectada,
        operacion,
        registro_id,
        usuario_id,
        valores_anteriores,
        valores_nuevos,
        fecha_hora
    ) VALUES (
        'ventas',
        'UPDATE',
        NEW.id,
        NEW.usuario_id,
        JSON_OBJECT(
            'id', OLD.id,
            'estado', OLD.estado,
            'subtotal', OLD.subtotal,
            'impuesto', OLD.impuesto,
            'total', OLD.total
        ),
        JSON_OBJECT(
            'id', NEW.id,
            'estado', NEW.estado,
            'subtotal', NEW.subtotal,
            'impuesto', NEW.impuesto,
            'total', NEW.total
        ),
        NOW()
    );
END$$

-- =====================================================
-- TRIGGERS PARA TABLA: devoluciones
-- =====================================================

CREATE TRIGGER tr_auditoria_devoluciones_insert
AFTER INSERT ON devoluciones
FOR EACH ROW
BEGIN
    INSERT INTO auditoria (
        tabla_afectada,
        operacion,
        registro_id,
        usuario_id,
        valores_anteriores,
        valores_nuevos,
        fecha_hora
    ) VALUES (
        'devoluciones',
        'INSERT',
        NEW.id,
        NEW.usuario_id,
        NULL,
        JSON_OBJECT(
            'id', NEW.id,
            'venta_id', NEW.venta_id,
            'producto_id', NEW.producto_id,
            'cantidad', NEW.cantidad,
            'valor_devolucion', NEW.valor_devolucion,
            'motivo', NEW.motivo
        ),
        NOW()
    );
END$$

CREATE TRIGGER tr_auditoria_devoluciones_update
AFTER UPDATE ON devoluciones
FOR EACH ROW
BEGIN
    INSERT INTO auditoria (
        tabla_afectada,
        operacion,
        registro_id,
        usuario_id,
        valores_anteriores,
        valores_nuevos,
        fecha_hora
    ) VALUES (
        'devoluciones',
        'UPDATE',
        NEW.id,
        NEW.usuario_id,
        JSON_OBJECT(
            'id', OLD.id,
            'cantidad', OLD.cantidad,
            'valor_devolucion', OLD.valor_devolucion,
            'motivo', OLD.motivo
        ),
        JSON_OBJECT(
            'id', NEW.id,
            'cantidad', NEW.cantidad,
            'valor_devolucion', NEW.valor_devolucion,
            'motivo', NEW.motivo
        ),
        NOW()
    );
END$$

CREATE TRIGGER tr_auditoria_devoluciones_delete
AFTER DELETE ON devoluciones
FOR EACH ROW
BEGIN
    INSERT INTO auditoria (
        tabla_afectada,
        operacion,
        registro_id,
        usuario_id,
        valores_anteriores,
        valores_nuevos,
        fecha_hora
    ) VALUES (
        'devoluciones',
        'DELETE',
        OLD.id,
        OLD.usuario_id,
        JSON_OBJECT(
            'id', OLD.id,
            'venta_id', OLD.venta_id,
            'producto_id', OLD.producto_id,
            'cantidad', OLD.cantidad,
            'valor_devolucion', OLD.valor_devolucion
        ),
        NULL,
        NOW()
    );
END$$

DELIMITER ;

-- =====================================================
-- VERIFICACIÓN DE TRIGGERS CREADOS
-- =====================================================

-- Mostrar todos los triggers de auditoría
SELECT 
    TRIGGER_NAME,
    EVENT_MANIPULATION,
    EVENT_OBJECT_TABLE,
    ACTION_TIMING
FROM information_schema.TRIGGERS
WHERE TRIGGER_SCHEMA = 'papeleria_jakake'
  AND TRIGGER_NAME LIKE 'tr_auditoria%'
ORDER BY EVENT_OBJECT_TABLE, ACTION_TIMING, EVENT_MANIPULATION;

SELECT 'Triggers de auditoría creados exitosamente' AS mensaje;
