# DOCUMENTACIÓN DE BASE DE DATOS
## Sistema de Gestión - Papelería JAKAKE

---

## 📌 INFORMACIÓN GENERAL

**Nombre del Sistema:** Base de Datos Papelería JAKAKE  
**Autores:** Kelly Palacio, Juan Esteban Albarán  
**Fecha:** Noviembre 2025  
**Motor de Base de Datos:** MySQL 5.7 o superior  
**Propósito:** Sistema de gestión integral para papelería con automatización mediante triggers, procedimientos almacenados y funciones.

---

## 🎯 DESCRIPCIÓN DEL SISTEMA

Este sistema de base de datos permite gestionar todas las operaciones de Papelería JAKAKE, incluyendo:

- Gestión de usuarios administrativos (administradores y cajeros)
- Registro de clientes sin necesidad de autenticación
- Control de inventario con actualización automática
- Gestión de proveedores
- Registro de ventas con cálculo automático de impuestos
- Procesamiento de devoluciones con generación automática de bonos
- Auditoría completa de todas las operaciones
- Cumplimiento de normativas de protección de datos (Ley 1581 de 2012)

---

## 📊 ESTRUCTURA DE TABLAS

### 1. **politicas_datos**
Almacena las versiones de políticas de protección de datos personales.

**Campos principales:**
- id, version, titulo, contenido, fecha_vigencia, activa

### 2. **usuarios**
Usuarios administrativos del sistema (administradores y cajeros).

**Campos principales:**
- id, nombre, email, password_hash, rol, telefono, estado

### 3. **clientes**
Clientes del negocio (no tienen acceso al sistema, solo datos para ventas).

**Campos principales:**
- id, nombre, cedula, telefono, email, fecha_nacimiento

### 4. **proveedores**
Proveedores que suministran productos.

**Campos principales:**
- id, nombre, nit, direccion, telefono, ciudad, estado

### 5. **productos**
Catálogo de productos en inventario.

**Campos principales:**
- id, codigo, nombre, precio, cantidad, proveedor_id, tipo

### 6. **movimientos_inventario**
Historial de entradas, salidas y ajustes de inventario.

**Campos principales:**
- id, producto_id, tipo_movimiento, cantidad_anterior, cantidad_nueva

### 7. **ventas**
Registro de todas las ventas realizadas.

**Campos principales:**
- id, fecha, cliente_id, usuario_id, subtotal, impuesto, total, estado

### 8. **detalle_venta**
Productos vendidos en cada venta.

**Campos principales:**
- id, venta_id, producto_id, cantidad, precio_unitario, subtotal

### 9. **devoluciones**
Registro de devoluciones de productos.

**Campos principales:**
- id, venta_id, producto_id, cantidad, motivo, valor_devolucion

### 10. **bonos_regalo**
Bonos generados automáticamente por devoluciones.

**Campos principales:**
- id, codigo, devolucion_id, cliente_id, valor, estado, fecha_vencimiento

### 11. **aceptacion_politicas**
Registro de aceptación de políticas por usuarios y clientes.

**Campos principales:**
- id, politica_id, tipo_usuario, usuario_id, cliente_id

### 12. **auditoria**
Registro automático de todas las operaciones CRUD.

**Campos principales:**
- id, tabla_afectada, operacion, registro_id, usuario_id, valores_anteriores, valores_nuevos

---

## ⚡ TRIGGERS IMPLEMENTADOS

### **Triggers de Auditoría (18 triggers)**

Registran automáticamente todas las operaciones INSERT, UPDATE y DELETE en las tablas principales.

**Tablas auditadas:**
- usuarios (INSERT, UPDATE, DELETE)
- clientes (INSERT, UPDATE, DELETE)
- proveedores (INSERT, UPDATE, DELETE)
- productos (INSERT, UPDATE, DELETE)
- ventas (INSERT, UPDATE)
- devoluciones (INSERT, UPDATE, DELETE)

**Funcionamiento:** Cada cambio se guarda en la tabla `auditoria` con valores anteriores y nuevos en formato JSON.

### **Triggers de Inventario (5 triggers)**

**1. tr_actualizar_stock_venta**
- Se ejecuta al insertar un detalle de venta
- Disminuye automáticamente la cantidad del producto
- Registra el movimiento en `movimientos_inventario`

**2. tr_actualizar_stock_devolucion**
- Se ejecuta al insertar una devolución
- Aumenta automáticamente la cantidad del producto
- Registra el movimiento en `movimientos_inventario`

**3. tr_registrar_movimiento_inventario**
- Se ejecuta al actualizar cantidad de un producto
- Registra ajustes manuales de inventario

**4. tr_validar_cantidad_positiva**
- Previene que la cantidad de productos sea negativa

**5. tr_alerta_stock_bajo**
- Genera alerta en auditoría cuando un producto está bajo en stock

### **Triggers de Seguridad (13 triggers)**

**1. tr_prevenir_eliminar_ventas**
- Impide eliminar ventas de la base de datos
- Las ventas solo pueden cambiar estado a "cancelada"

**2. tr_validar_stock_venta**
- Verifica que haya stock suficiente antes de registrar una venta
- Impide ventas si no hay cantidad disponible

**3. tr_validar_devolucion**
- Verifica que el producto fue vendido en esa venta
- Valida que no se devuelva más de lo vendido
- Calcula automáticamente el valor de devolución

**4-7. Validación de emails y cédulas**
- Garantizan que emails de usuarios sean únicos
- Garantizan que cédulas de clientes sean únicas

**8-9. Validación de precios**
- Aseguran que los precios sean siempre positivos

**10. tr_validar_totales_venta**
- Verifica consistencia en cálculos de subtotal, impuesto y total

### **Triggers de Bonos (3 triggers + 1 evento)**

**1. tr_generar_bono_devolucion**
- Se ejecuta automáticamente al registrar una devolución
- Crea un bono de regalo con el valor de la devolución
- Genera código único (formato: BONO-YYYYMMDD-ID)
- Establece fecha de vencimiento (90 días)

**2. tr_validar_estado_bono**
- Impide usar bonos vencidos
- Previene cambios en bonos ya usados

**3. tr_validar_uso_bono**
- Verifica que el bono sea del cliente correcto
- Valida que la venta esté completada

**4. evt_marcar_bonos_vencidos (Evento)**
- Se ejecuta diariamente de forma automática
- Marca como "vencidos" los bonos cuya fecha ha pasado

---

## 🔧 PROCEDIMIENTOS ALMACENADOS

### **1. sp_calcular_totales_venta**
Calcula el IVA (19%) y total de una venta.

**Parámetros:**
```sql
IN p_subtotal DECIMAL(10,2)
OUT p_impuesto DECIMAL(10,2)
OUT p_total DECIMAL(10,2)
```

### **2. sp_registrar_venta_completa**
Registra una venta con múltiples productos en una sola transacción.

**Parámetros:**
```sql
IN p_cliente_id INT
IN p_usuario_id INT
IN p_productos JSON
OUT p_venta_id INT
OUT p_total_final DECIMAL(10,2)
```

**Ejemplo de uso:**
```sql
CALL sp_registrar_venta_completa(
    1, 
    3, 
    '[{"producto_id": 1, "cantidad": 2, "precio_unitario": 4500}]',
    @venta_id,
    @total
);
```

### **3. sp_procesar_devolucion**
Procesa una devolución y genera bono automáticamente.

**Parámetros:**
```sql
IN p_venta_id INT
IN p_producto_id INT
IN p_cantidad INT
IN p_motivo TEXT
IN p_usuario_id INT
OUT p_devolucion_id INT
OUT p_bono_codigo VARCHAR(50)
```

### **4. sp_historial_compras_cliente**
Muestra todas las compras de un cliente.

**Parámetros:**
```sql
IN p_cliente_id INT
```

**Ejemplo de uso:**
```sql
CALL sp_historial_compras_cliente(1);
```

### **5. sp_reporte_ventas_periodo**
Genera reporte de ventas entre dos fechas.

**Parámetros:**
```sql
IN p_fecha_inicio DATE
IN p_fecha_fin DATE
```

**Ejemplo de uso:**
```sql
CALL sp_reporte_ventas_periodo('2025-01-01', '2025-12-31');
```

### **6. sp_productos_bajo_stock**
Lista productos que necesitan reabastecimiento.

**Parámetros:**
```sql
IN p_cantidad_minima INT
```

### **7. sp_auditoria_reciente**
Muestra registros de auditoría de los últimos días.

**Parámetros:**
```sql
IN p_dias INT
```

### **8. sp_productos_mas_vendidos**
Top productos más vendidos en un período.

**Parámetros:**
```sql
IN p_fecha_inicio DATE
IN p_fecha_fin DATE
IN p_limite INT
```

### **9. sp_resumen_cliente**
Información completa de un cliente (datos, estadísticas, bonos).

**Parámetros:**
```sql
IN p_cliente_id INT
```

### **10. sp_aplicar_bono_venta**
Aplica un bono de regalo a una venta.

**Parámetros:**
```sql
IN p_bono_codigo VARCHAR(50)
IN p_venta_id INT
```

---

## 📐 FUNCIONES SQL

### **Funciones de Cálculo**

**fn_calcular_iva(subtotal)** - Retorna el IVA (19%)  
**fn_aplicar_descuento(monto, porcentaje)** - Calcula precio con descuento  
**fn_calcular_margen(precio_venta, precio_costo)** - Calcula margen de ganancia

### **Funciones de Inventario**

**fn_stock_disponible(producto_id)** - Retorna cantidad disponible  
**fn_validar_stock_suficiente(producto_id, cantidad)** - Verifica si hay stock  
**fn_rotacion_producto(producto_id, dias)** - Calcula rotación de inventario

### **Funciones de Clientes**

**fn_edad_cliente(fecha_nacimiento)** - Calcula edad en años  
**fn_total_ventas_cliente(cliente_id)** - Total gastado por cliente  
**fn_contar_compras_cliente(cliente_id)** - Número de compras  
**fn_promedio_venta_cliente(cliente_id)** - Ticket promedio  
**fn_dias_ultima_compra(cliente_id)** - Días desde última compra

### **Funciones de Bonos**

**fn_verificar_bono_activo(codigo)** - Verifica si un bono es válido  
**fn_valor_bonos_cliente(cliente_id)** - Suma de bonos activos

### **Funciones Auxiliares**

**fn_nombre_producto(producto_id)** - Retorna nombre del producto  
**fn_proveedor_activo(proveedor_id)** - Verifica si proveedor está activo

---

## 🚀 INSTALACIÓN

### **Paso 1: Crear la base de datos**
```sql
SOURCE 01_crear_tablas.sql
```

### **Paso 2: Crear triggers de auditoría**
```sql
SOURCE 02_triggers_auditoria.sql
```

### **Paso 3: Crear triggers de inventario**
```sql
SOURCE 03_triggers_inventario.sql
```

### **Paso 4: Crear triggers de seguridad**
```sql
SOURCE 04_triggers_seguridad.sql
```

### **Paso 5: Crear triggers de bonos**
```sql
SOURCE 05_trigger_bonos.sql
```

### **Paso 6: Crear procedimientos**
```sql
SOURCE 06_procedimientos.sql
```

### **Paso 7: Crear funciones**
```sql
SOURCE 07_funciones.sql
```

### **Paso 8: Crear índices**
```sql
SOURCE 08_indices.sql
```

### **Paso 9: Cargar datos de prueba**
```sql
SOURCE 09_datos_prueba.sql
```

---

## 🧪 PRUEBAS BÁSICAS

### **Probar inserción de venta**
```sql
CALL sp_registrar_venta_completa(
    1, 
    3, 
    '[{"producto_id": 1, "cantidad": 2, "precio_unitario": 4500}]',
    @venta_id,
    @total
);
SELECT @venta_id, @total;
```

### **Probar devolución con bono**
```sql
CALL sp_procesar_devolucion(1, 1, 1, 'Producto defectuoso', 3, @dev_id, @bono);
SELECT @dev_id, @bono;
```

### **Ver auditoría**
```sql
CALL sp_auditoria_reciente(7);
```

### **Ver productos bajo stock**
```sql
CALL sp_productos_bajo_stock(20);
```

---

## 📈 RESUMEN TÉCNICO

- **Total de tablas:** 12
- **Total de triggers:** 39
- **Total de procedimientos:** 10
- **Total de funciones:** 15
- **Total de índices:** 30+ (incluyendo UNIQUE, compuestos y FULLTEXT)
- **Normalización:** 3FN (Tercera Forma Normal)

---

## ⚠️ NOTAS IMPORTANTES

1. **Las ventas NO se pueden eliminar**, solo cambiar estado a "cancelada"
2. **Los bonos se generan automáticamente** al registrar devoluciones
3. **El inventario se actualiza automáticamente** en cada venta/devolución
4. **Todas las operaciones quedan registradas** en la tabla de auditoría
5. **Las contraseñas se almacenan con hash** (usar `password_hash()` en PHP)

---

## 👥 ROLES DE USUARIO

**Administrador:**
- Acceso completo al sistema
- Puede gestionar usuarios, productos, proveedores
- Consultar todos los reportes

**Cajero:**
- Registrar ventas
- Procesar devoluciones
- Consultar inventario
- No puede eliminar ni modificar configuraciones

**Cliente:**
- No tiene acceso al sistema
- Solo se registran sus datos para ventas

---

## 📞 SOPORTE

Para consultas sobre el sistema, contactar a los desarrolladores del proyecto.

**Fecha de última actualización:** Noviembre 27, 2025
