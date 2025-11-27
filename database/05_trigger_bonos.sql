-- =====================================================
-- TRIGGER DE GENERACIÓN DE BONOS
-- Sistema de Base de Datos - Papelería JAKAKE
-- =====================================================
-- Descripción: Genera automáticamente bonos de regalo
--              al registrar una devolución
-- Autor: Persona 1
-- Fecha: 2025-11-26
-- =====================================================

USE papeleria_jakake;

DELIMITER $$

-- =====================================================
-- TRIGGER: Generar bono automáticamente
-- Se ejecuta AFTER INSERT en devoluciones
-- Crea un bono de regalo con el valor de la devolución
-- =====================================================

CREATE TRIGGER tr_generar_bono_devolucion
AFTER INSERT ON devoluciones
FOR EACH ROW
BEGIN
    DECLARE v_cliente_id INT;
    DECLARE v_codigo_bono VARCHAR(50);
    DECLARE v_fecha_vencimiento DATE;
    
    -- Obtener el cliente_id de la venta original
    SELECT cliente_id INTO v_cliente_id
    FROM ventas
    WHERE id = NEW.venta_id;
    
    -- Si la venta no tiene cliente asociado, no se puede crear bono
    IF v_cliente_id IS NULL THEN
        SIGNAL SQLSTATE '45000'
        SET MESSAGE_TEXT = 'Error: No se puede generar bono para una venta sin cliente registrado',
            MYSQL_ERRNO = 3001;
    END IF;
    
    -- Generar código único para el bono (formato: BONO-YYYYMMDD-ID)
    SET v_codigo_bono = CONCAT(
        'BONO-',
        DATE_FORMAT(NOW(), '%Y%m%d'),
        '-',
        LPAD(NEW.id, 6, '0')
    );
    
    -- Calcular fecha de vencimiento (90 días desde la emisión)
    SET v_fecha_vencimiento = DATE_ADD(CURDATE(), INTERVAL 90 DAY);
    
    -- Insertar bono de regalo
    INSERT INTO bonos_regalo (
        codigo,
        devolucion_id,
        cliente_id,
        valor,
        estado,
        fecha_emision,
        fecha_vencimiento,
        fecha_uso,
        venta_uso_id
    ) VALUES (
        v_codigo_bono,
        NEW.id,
        v_cliente_id,
        NEW.valor_devolucion,
        'activo',
        CURDATE(),
        v_fecha_vencimiento,
        NULL,
        NULL
    );
    
    -- Registrar en auditoría la creación del bono
    INSERT INTO auditoria (
        tabla_afectada,
        operacion,
        registro_id,
        usuario_id,
        valores_anteriores,
        valores_nuevos,
        fecha_hora
    ) VALUES (
        'bonos_regalo',
        'INSERT',
        LAST_INSERT_ID(),
        NEW.usuario_id,
        NULL,
        JSON_OBJECT(
            'codigo', v_codigo_bono,
            'devolucion_id', NEW.id,
            'cliente_id', v_cliente_id,
            'valor', NEW.valor_devolucion,
            'fecha_vencimiento', v_fecha_vencimiento,
            'mensaje', 'Bono generado automáticamente por devolución'
        ),
        NOW()
    );
END$$

-- =====================================================
-- TRIGGER: Actualizar estado de bonos vencidos
-- Se ejecuta BEFORE UPDATE en bonos_regalo
-- Cambia estado a 'vencido' si ya pasó la fecha
-- =====================================================

CREATE TRIGGER tr_validar_estado_bono
BEFORE UPDATE ON bonos_regalo
FOR EACH ROW
BEGIN
    -- Si se intenta usar un bono vencido
    IF NEW.estado = 'usado' AND OLD.estado = 'activo' THEN
        IF CURDATE() > OLD.fecha_vencimiento THEN
            SIGNAL SQLSTATE '45000'
            SET MESSAGE_TEXT = 'Error: El bono ya está vencido y no puede ser utilizado',
                MYSQL_ERRNO = 3002;
        END IF;
        
        -- Registrar fecha de uso
        SET NEW.fecha_uso = CURDATE();
    END IF;
    
    -- Prevenir cambio de estado de bono usado o vencido
    IF OLD.estado IN ('usado', 'vencido') AND NEW.estado != OLD.estado THEN
        SIGNAL SQLSTATE '45000'
        SET MESSAGE_TEXT = 'Error: No se puede cambiar el estado de un bono usado o vencido',
            MYSQL_ERRNO = 3003;
    END IF;
END$$

-- =====================================================
-- TRIGGER: Validar uso de bono en venta
-- Se ejecuta BEFORE UPDATE en bonos_regalo
-- Verifica que el bono sea válido y del cliente correcto
-- =====================================================

CREATE TRIGGER tr_validar_uso_bono
BEFORE UPDATE ON bonos_regalo
FOR EACH ROW
BEGIN
    DECLARE v_cliente_venta INT;
    
    -- Solo validar cuando se marca como 'usado'
    IF NEW.estado = 'usado' AND OLD.estado = 'activo' THEN
        
        -- Verificar que se proporcionó venta_uso_id
        IF NEW.venta_uso_id IS NULL THEN
            SIGNAL SQLSTATE '45000'
            SET MESSAGE_TEXT = 'Error: Debe especificar la venta donde se usa el bono',
                MYSQL_ERRNO = 3004;
        END IF;
        
        -- Obtener cliente de la venta
        SELECT cliente_id INTO v_cliente_venta
        FROM ventas
        WHERE id = NEW.venta_uso_id;
        
        -- Validar que el cliente de la venta sea el mismo del bono
        IF v_cliente_venta != OLD.cliente_id THEN
            SIGNAL SQLSTATE '45000'
            SET MESSAGE_TEXT = 'Error: El bono solo puede ser usado por el cliente asociado',
                MYSQL_ERRNO = 3005;
        END IF;
        
        -- Validar que la venta no esté cancelada
        IF NOT EXISTS (
            SELECT 1 FROM ventas 
            WHERE id = NEW.venta_uso_id 
              AND estado = 'completada'
        ) THEN
            SIGNAL SQLSTATE '45000'
            SET MESSAGE_TEXT = 'Error: La venta debe estar completada para usar el bono',
                MYSQL_ERRNO = 3006;
        END IF;
    END IF;
END$$

-- =====================================================
-- EVENT: Marcar bonos vencidos automáticamente
-- Se ejecuta diariamente para actualizar bonos vencidos
-- =====================================================

-- Habilitar event scheduler (si no está habilitado)
SET GLOBAL event_scheduler = ON;

-- Crear evento para marcar bonos vencidos
CREATE EVENT IF NOT EXISTS evt_marcar_bonos_vencidos
ON SCHEDULE EVERY 1 DAY
STARTS CURRENT_DATE + INTERVAL 1 DAY
DO
BEGIN
    -- Actualizar bonos que ya vencieron
    UPDATE bonos_regalo
    SET estado = 'vencido'
    WHERE estado = 'activo'
      AND fecha_vencimiento < CURDATE();
    
    -- Registrar en auditoría
    IF ROW_COUNT() > 0 THEN
        INSERT INTO auditoria (
            tabla_afectada,
            operacion,
            registro_id,
            usuario_id,
            valores_anteriores,
            valores_nuevos,
            fecha_hora
        ) VALUES (
            'bonos_regalo',
            'UPDATE',
            0,
            NULL,
            NULL,
            JSON_OBJECT(
                'accion', 'Evento automático: marcar bonos vencidos',
                'bonos_actualizados', ROW_COUNT()
            ),
            NOW()
        );
    END IF;
END$$

DELIMITER ;

-- =====================================================
-- VERIFICACIÓN DE TRIGGER Y EVENTO CREADOS
-- =====================================================

-- Verificar triggers de bonos
SELECT 
    TRIGGER_NAME,
    EVENT_MANIPULATION,
    EVENT_OBJECT_TABLE,
    ACTION_TIMING
FROM information_schema.TRIGGERS
WHERE TRIGGER_SCHEMA = 'papeleria_jakake'
  AND TRIGGER_NAME LIKE '%bono%'
ORDER BY EVENT_OBJECT_TABLE, ACTION_TIMING;

-- Verificar evento creado
SELECT 
    EVENT_NAME,
    EVENT_TYPE,
    STATUS,
    STARTS,
    INTERVAL_VALUE,
    INTERVAL_FIELD
FROM information_schema.EVENTS
WHERE EVENT_SCHEMA = 'papeleria_jakake';

SELECT 'Trigger de generación de bonos y evento de vencimiento creados exitosamente' AS mensaje;
