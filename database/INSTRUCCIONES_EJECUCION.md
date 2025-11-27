# GUÍA DE EJECUCIÓN Y PRUEBAS
## Sistema de Base de Datos - Papelería JAKAKE

---

## 📋 PREREQUISITOS

Antes de comenzar, asegúrate de tener:

1. **MySQL 5.7 o superior** instalado y ejecutándose
2. **Cliente MySQL** (MySQL Workbench, CLI, phpMyAdmin, etc.)
3. **Permisos de administrador** para crear bases de datos
4. **Acceso a la carpeta** `database/` con todos los archivos SQL

---

## 🚀 PASOS DE INSTALACIÓN

### Paso 1: Conectar a MySQL

**Opción A: MySQL Command Line**
```bash
mysql -u root -p
```

**Opción B: MySQL Workbench**
- Abrir MySQL Workbench
- Conectar a la instancia local
- Abrir una nueva ventana de consultas

---

### Paso 2: Ejecutar Scripts en Orden

#### 📄 Script 1: Crear Base de Datos y Tablas
```sql
SOURCE 01_crear_tablas.sql;
```

**Verificar:**
```sql
USE papeleria_jakake;
SHOW TABLES;
```
**Resultado esperado:** 12 tablas creadas

---

#### 📄 Script 2: Triggers de Auditoría
```sql
SOURCE 02_triggers_auditoria.sql;
```

**Verificar:**
```sql
SHOW TRIGGERS LIKE 'tr_auditoria%';
```
**Resultado esperado:** 18 triggers

---

#### 📄 Script 3: Triggers de Inventario
```sql
SOURCE 03_triggers_inventario.sql;
```

**Verificar:**
```sql
SHOW TRIGGERS WHERE `Trigger` LIKE '%stock%' OR `Trigger` LIKE '%inventario%';
```
**Resultado esperado:** 5 triggers

---

#### 📄 Script 4: Triggers de Seguridad
```sql
SOURCE 04_triggers_seguridad.sql;
```

**Verificar:**
```sql
SHOW TRIGGERS WHERE `Trigger` LIKE 'tr_validar%' OR `Trigger` LIKE 'tr_prevenir%';
```
**Resultado esperado:** 13 triggers

---

#### 📄 Script 5: Triggers de Bonos
```sql
SOURCE 05_trigger_bonos.sql;
```

**Verificar triggers:**
```sql
SHOW TRIGGERS WHERE `Trigger` LIKE '%bono%';
```
**Resultado esperado:** 3 triggers

**Verificar evento:**
```sql
SHOW EVENTS;
```
**Resultado esperado:** 1 evento (evt_marcar_bonos_vencidos)

**IMPORTANTE:** Activar el Event Scheduler
```sql
SET GLOBAL event_scheduler = ON;
```

---

#### 📄 Script 6: Procedimientos Almacenados
```sql
SOURCE 06_procedimientos.sql;
```

**Verificar:**
```sql
SHOW PROCEDURE STATUS WHERE Db = 'papeleria_jakake';
```
**Resultado esperado:** 10 procedimientos

---

#### 📄 Script 7: Funciones
```sql
SOURCE 07_funciones.sql;
```

**Verificar:**
```sql
SHOW FUNCTION STATUS WHERE Db = 'papeleria_jakake';
```
**Resultado esperado:** 15 funciones

---

#### 📄 Script 8: Índices
```sql
SOURCE 08_indices.sql;
```

**Verificar:**
```sql
SHOW INDEX FROM productos;
SHOW INDEX FROM ventas;
```
**Resultado esperado:** Índices adicionales creados

---

#### 📄 Script 9: Datos de Prueba
```sql
SOURCE 09_datos_prueba.sql;
```

**Verificar:**
```sql
SELECT COUNT(*) AS total_usuarios FROM usuarios;
SELECT COUNT(*) AS total_clientes FROM clientes;
SELECT COUNT(*) AS total_productos FROM productos;
SELECT COUNT(*) AS total_ventas FROM ventas;
```

**Resultado esperado:**
- 4 usuarios (2 administradores, 2 cajeros)
- 10 clientes
- 30 productos
- 15 ventas

---

## 🧪 PRUEBAS FUNCIONALES

### Prueba 1: Verificar Trigger de Auditoría

**Insertar un cliente:**
```sql
INSERT INTO clientes (nombre, cedula, telefono, email, fecha_nacimiento, politica_id, acepto_politicas)
VALUES ('Cliente Prueba', '9999999999', '3001234567', 'prueba@test.com', '1995-01-01', 1, TRUE);
```

**Verificar auditoría:**
```sql
SELECT * FROM auditoria WHERE tabla_afectada = 'clientes' ORDER BY fecha_hora DESC LIMIT 1;
```

**Resultado esperado:** 1 registro en auditoría con operación INSERT

---

### Prueba 2: Verificar Trigger de Inventario

**Consultar stock antes:**
```sql
SELECT id, nombre, cantidad FROM productos WHERE id = 1;
```

**Registrar una venta con procedimiento:**
```sql
CALL sp_registrar_venta_completa(
    1,  -- cliente_id
    3,  -- usuario_id (cajero)
    '[{"producto_id": 1, "cantidad": 2, "precio_unitario": 4500}]',
    @venta_id,
    @total
);

SELECT @venta_id AS venta_creada, @total AS total_venta;
```

**Verificar stock después:**
```sql
SELECT id, nombre, cantidad FROM productos WHERE id = 1;
```

**Resultado esperado:** La cantidad disminuyó en 2 unidades

**Verificar movimiento:**
```sql
SELECT * FROM movimientos_inventario WHERE producto_id = 1 ORDER BY fecha_movimiento DESC LIMIT 1;
```

---

### Prueba 3: Verificar Generación Automática de Bonos

**Procesar una devolución:**
```sql
CALL sp_procesar_devolucion(
    1,  -- venta_id
    1,  -- producto_id
    1,  -- cantidad
    'Producto defectuoso para prueba',
    3,  -- usuario_id
    @devolucion_id,
    @codigo_bono
);

SELECT @devolucion_id AS devolucion, @codigo_bono AS bono_generado;
```

**Verificar bono creado:**
```sql
SELECT * FROM bonos_regalo WHERE codigo = @codigo_bono;
```

**Resultado esperado:** 
- Bono creado con estado 'activo'
- Fecha de vencimiento = fecha_emision + 90 días
- Valor = valor de la devolución

---

### Prueba 4: Verificar Validación de Stock

**Intentar vender más de lo disponible:**
```sql
-- Consultar stock actual
SELECT id, nombre, cantidad FROM productos WHERE id = 2;

-- Intentar vender 1000 unidades (debería fallar si no hay suficiente)
INSERT INTO detalle_venta (venta_id, producto_id, cantidad, precio_unitario, subtotal)
VALUES (1, 2, 1000, 5000, 5000000);
```

**Resultado esperado:** Error del trigger `tr_validar_stock_venta`

---

### Prueba 5: Probar Función de Cálculo de IVA

```sql
SELECT fn_calcular_iva(10000) AS iva_calculado;
```

**Resultado esperado:** 1900.00 (19% de 10000)

---

### Prueba 6: Verificar Función de Stock

```sql
SELECT 
    id,
    nombre,
    cantidad AS stock_tabla,
    fn_stock_disponible(id) AS stock_funcion
FROM productos
WHERE id = 1;
```

**Resultado esperado:** Ambos valores deben coincidir

---

### Prueba 7: Consultar Historial de Cliente

```sql
CALL sp_historial_compras_cliente(1);
```

**Resultado esperado:** Lista de todas las compras del cliente ID 1

---

### Prueba 8: Reporte de Ventas por Período

```sql
CALL sp_reporte_ventas_periodo('2025-01-01', '2025-12-31');
```

**Resultado esperado:** Resumen de ventas del año 2025

---

### Prueba 9: Productos Bajo Stock

```sql
CALL sp_productos_bajo_stock(20);
```

**Resultado esperado:** Lista de productos con cantidad menor a 20

---

### Prueba 10: Productos Más Vendidos

```sql
CALL sp_productos_mas_vendidos('2025-01-01', '2025-12-31', 10);
```

**Resultado esperado:** Top 10 productos más vendidos

---

### Prueba 11: Intentar Eliminar una Venta (Debe Fallar)

```sql
DELETE FROM ventas WHERE id = 1;
```

**Resultado esperado:** Error del trigger `tr_prevenir_eliminar_ventas`
**Mensaje esperado:** "No se pueden eliminar ventas. Cambie el estado a 'cancelada'."

---

### Prueba 12: Verificar Evento de Bonos Vencidos

**Crear un bono con fecha vencida manualmente:**
```sql
INSERT INTO bonos_regalo (codigo, devolucion_id, cliente_id, valor, estado, fecha_emision, fecha_vencimiento)
VALUES ('BONO-TEST-999', 1, 1, 5000, 'activo', '2024-01-01', '2024-04-01');
```

**Esperar 1 minuto (el evento corre cada día a las 00:00) o ejecutar manualmente:**
```sql
UPDATE bonos_regalo 
SET estado = 'vencido' 
WHERE estado = 'activo' 
  AND fecha_vencimiento < CURDATE();
```

**Verificar:**
```sql
SELECT * FROM bonos_regalo WHERE codigo = 'BONO-TEST-999';
```

**Resultado esperado:** Estado cambió a 'vencido'

---

## 📊 CONSULTAS DE VALIDACIÓN GENERAL

### Verificar Integridad Total

```sql
-- Contar registros en todas las tablas
SELECT 'politicas_datos' AS tabla, COUNT(*) AS registros FROM politicas_datos
UNION ALL
SELECT 'usuarios', COUNT(*) FROM usuarios
UNION ALL
SELECT 'clientes', COUNT(*) FROM clientes
UNION ALL
SELECT 'proveedores', COUNT(*) FROM proveedores
UNION ALL
SELECT 'productos', COUNT(*) FROM productos
UNION ALL
SELECT 'ventas', COUNT(*) FROM ventas
UNION ALL
SELECT 'detalle_venta', COUNT(*) FROM detalle_venta
UNION ALL
SELECT 'devoluciones', COUNT(*) FROM devoluciones
UNION ALL
SELECT 'bonos_regalo', COUNT(*) FROM bonos_regalo
UNION ALL
SELECT 'movimientos_inventario', COUNT(*) FROM movimientos_inventario
UNION ALL
SELECT 'aceptacion_politicas', COUNT(*) FROM aceptacion_politicas
UNION ALL
SELECT 'auditoria', COUNT(*) FROM auditoria;
```

---

### Verificar Triggers Activos

```sql
SELECT 
    TRIGGER_NAME,
    EVENT_MANIPULATION,
    EVENT_OBJECT_TABLE,
    ACTION_TIMING
FROM information_schema.TRIGGERS
WHERE TRIGGER_SCHEMA = 'papeleria_jakake'
ORDER BY EVENT_OBJECT_TABLE, ACTION_TIMING, EVENT_MANIPULATION;
```

**Resultado esperado:** 39 triggers

---

### Verificar Procedimientos y Funciones

```sql
SELECT 
    ROUTINE_TYPE,
    ROUTINE_NAME,
    DATA_TYPE AS return_type
FROM information_schema.ROUTINES
WHERE ROUTINE_SCHEMA = 'papeleria_jakake'
ORDER BY ROUTINE_TYPE, ROUTINE_NAME;
```

**Resultado esperado:** 10 procedimientos + 15 funciones = 25 rutinas

---

## ⚠️ SOLUCIÓN DE PROBLEMAS

### Error: "Event Scheduler is OFF"

**Solución:**
```sql
SET GLOBAL event_scheduler = ON;
```

Para hacerlo permanente, editar `my.cnf` o `my.ini`:
```ini
[mysqld]
event_scheduler=ON
```

---

### Error: "Access denied for user"

**Solución:** Verificar permisos del usuario
```sql
GRANT ALL PRIVILEGES ON papeleria_jakake.* TO 'tu_usuario'@'localhost';
FLUSH PRIVILEGES;
```

---

### Error al ejecutar SOURCE

**Solución alternativa:** Copiar y pegar el contenido del archivo SQL directamente en el cliente MySQL

---

### Error: "DELIMITER command not recognized"

**Solución:** 
- En MySQL Workbench: ejecutar directamente
- En MySQL CLI: usar `SOURCE` en lugar de copiar/pegar
- En phpMyAdmin: cambiar el delimitador en la interfaz

---

## ✅ CHECKLIST DE VERIFICACIÓN FINAL

- [ ] Base de datos `papeleria_jakake` creada
- [ ] 12 tablas creadas sin errores
- [ ] 39 triggers activos
- [ ] 10 procedimientos creados
- [ ] 15 funciones creadas
- [ ] Event Scheduler activado
- [ ] 1 evento activo (evt_marcar_bonos_vencidos)
- [ ] Datos de prueba cargados
- [ ] Auditoría funciona correctamente
- [ ] Inventario se actualiza automáticamente
- [ ] Bonos se generan automáticamente
- [ ] Validaciones de seguridad funcionan
- [ ] Ventas no se pueden eliminar
- [ ] Procedimientos ejecutan sin errores
- [ ] Funciones retornan valores correctos

---

## 📈 PRÓXIMOS PASOS

Una vez completadas las pruebas:

1. **Integración con PHP:**
   - Usar los procedimientos almacenados desde PHP con `mysqli` o `PDO`
   - Ejemplo: `CALL sp_registrar_venta_completa(?, ?, ?, @v, @t)`

2. **Backups:**
   ```bash
   mysqldump -u root -p papeleria_jakake > backup_jakake.sql
   ```

3. **Monitoreo:**
   - Revisar tabla `auditoria` regularmente
   - Consultar productos bajo stock con `sp_productos_bajo_stock()`
   - Generar reportes de ventas con `sp_reporte_ventas_periodo()`

---

**Fecha de creación:** Noviembre 27, 2025  
**Autores:** Kelly Palacio, Juan Esteban Albarán
