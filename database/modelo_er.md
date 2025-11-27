# MODELO ENTIDAD-RELACIÓN (ERD)
## Sistema de Base de Datos - Papelería JAKAKE

---

## 📊 ENTIDADES PRINCIPALES

### 1. **USUARIOS**
- **Descripción:** Usuarios administrativos del sistema (administradores y cajeros)
- **Atributos:**
  - id (PK)
  - nombre
  - email (UNIQUE)
  - password_hash
  - rol (ENUM: 'administrador', 'cajero')
  - telefono
  - direccion
  - estado (ENUM: 'activo', 'inactivo')
  - fecha_registro
  - acepto_politicas (BOOLEAN)
  - politica_id (FK → politicas_datos)

### 2. **CLIENTES**
- **Descripción:** Clientes del negocio (sin autenticación en el sistema)
- **Atributos:**
  - id (PK)
  - nombre
  - cedula (UNIQUE)
  - telefono
  - direccion
  - email
  - fecha_nacimiento
  - fecha_registro
  - acepto_politicas (BOOLEAN)
  - politica_id (FK → politicas_datos)

### 3. **PROVEEDORES**
- **Descripción:** Proveedores que suministran productos
- **Atributos:**
  - id (PK)
  - nombre
  - nit (UNIQUE)
  - direccion
  - telefono
  - contacto (nombre persona de contacto)
  - ciudad
  - email
  - estado (ENUM: 'activo', 'inactivo')
  - fecha_registro

### 4. **PRODUCTOS**
- **Descripción:** Productos en inventario
- **Atributos:**
  - id (PK)
  - codigo (UNIQUE)
  - nombre
  - descripcion
  - precio (DECIMAL)
  - cantidad (INT)
  - cantidad_minima (para alertas)
  - proveedor_id (FK → proveedores)
  - tipo (ENUM: 'escolar', 'oficina', 'arte', 'otro')
  - estado (ENUM: 'activo', 'inactivo')
  - fecha_ingreso
  - fecha_actualizacion

### 5. **MOVIMIENTOS_INVENTARIO**
- **Descripción:** Historial de cambios en inventario
- **Atributos:**
  - id (PK)
  - producto_id (FK → productos)
  - tipo_movimiento (ENUM: 'entrada', 'salida', 'ajuste')
  - cantidad_anterior
  - cantidad_nueva
  - cantidad_movimiento
  - motivo
  - usuario_id (FK → usuarios)
  - fecha_movimiento

### 6. **VENTAS**
- **Descripción:** Registro de ventas realizadas
- **Atributos:**
  - id (PK)
  - fecha
  - cliente_id (FK → clientes, NULL si es venta sin cliente registrado)
  - usuario_id (FK → usuarios - cajero que realizó la venta)
  - subtotal (DECIMAL)
  - impuesto (DECIMAL - IVA 19%)
  - total (DECIMAL)
  - estado (ENUM: 'completada', 'cancelada')
  - fecha_registro

### 7. **DETALLE_VENTA**
- **Descripción:** Productos vendidos en cada venta
- **Atributos:**
  - id (PK)
  - venta_id (FK → ventas)
  - producto_id (FK → productos)
  - cantidad
  - precio_unitario (precio al momento de la venta)
  - subtotal (cantidad * precio_unitario)

### 8. **DEVOLUCIONES**
- **Descripción:** Devoluciones de productos
- **Atributos:**
  - id (PK)
  - venta_id (FK → ventas)
  - producto_id (FK → productos)
  - cantidad
  - motivo (TEXT)
  - valor_devolucion (DECIMAL)
  - usuario_id (FK → usuarios - quien procesa)
  - fecha_devolucion
  - fecha_registro

### 9. **BONOS_REGALO**
- **Descripción:** Bonos generados por devoluciones
- **Atributos:**
  - id (PK)
  - codigo (UNIQUE - generado automáticamente)
  - devolucion_id (FK → devoluciones)
  - cliente_id (FK → clientes)
  - valor (DECIMAL)
  - estado (ENUM: 'activo', 'usado', 'vencido')
  - fecha_emision
  - fecha_vencimiento
  - fecha_uso (NULL hasta que se use)
  - venta_uso_id (FK → ventas, NULL hasta que se use)

### 10. **POLITICAS_DATOS**
- **Descripción:** Versiones de políticas de protección de datos
- **Atributos:**
  - id (PK)
  - version
  - titulo
  - contenido (TEXT)
  - fecha_vigencia
  - activa (BOOLEAN)

### 11. **ACEPTACION_POLITICAS**
- **Descripción:** Registro de aceptación de políticas
- **Atributos:**
  - id (PK)
  - politica_id (FK → politicas_datos)
  - tipo_usuario (ENUM: 'usuario', 'cliente')
  - usuario_id (FK → usuarios, NULL si es cliente)
  - cliente_id (FK → clientes, NULL si es usuario)
  - fecha_aceptacion
  - ip_aceptacion

### 12. **AUDITORIA**
- **Descripción:** Registro automático de todas las operaciones CRUD
- **Atributos:**
  - id (PK)
  - tabla_afectada
  - operacion (ENUM: 'INSERT', 'UPDATE', 'DELETE')
  - registro_id (ID del registro afectado)
  - usuario_id (FK → usuarios, NULL si es operación del sistema)
  - valores_anteriores (JSON)
  - valores_nuevos (JSON)
  - fecha_hora
  - ip_usuario

---

## 🔗 RELACIONES

### Relación 1:N (Uno a Muchos)

1. **PROVEEDORES → PRODUCTOS**
   - Cardinalidad: Un proveedor puede suministrar muchos productos
   - FK: productos.proveedor_id → proveedores.id

2. **USUARIOS → VENTAS**
   - Cardinalidad: Un usuario (cajero) puede realizar muchas ventas
   - FK: ventas.usuario_id → usuarios.id

3. **CLIENTES → VENTAS**
   - Cardinalidad: Un cliente puede tener muchas ventas
   - FK: ventas.cliente_id → clientes.id (NULLABLE)

4. **VENTAS → DETALLE_VENTA**
   - Cardinalidad: Una venta puede tener muchos detalles (productos)
   - FK: detalle_venta.venta_id → ventas.id

5. **PRODUCTOS → DETALLE_VENTA**
   - Cardinalidad: Un producto puede estar en muchas ventas
   - FK: detalle_venta.producto_id → productos.id

6. **PRODUCTOS → MOVIMIENTOS_INVENTARIO**
   - Cardinalidad: Un producto puede tener muchos movimientos
   - FK: movimientos_inventario.producto_id → productos.id

7. **USUARIOS → MOVIMIENTOS_INVENTARIO**
   - Cardinalidad: Un usuario puede registrar muchos movimientos
   - FK: movimientos_inventario.usuario_id → usuarios.id

8. **VENTAS → DEVOLUCIONES**
   - Cardinalidad: Una venta puede tener muchas devoluciones
   - FK: devoluciones.venta_id → ventas.id

9. **PRODUCTOS → DEVOLUCIONES**
   - Cardinalidad: Un producto puede ser devuelto muchas veces
   - FK: devoluciones.producto_id → productos.id

10. **DEVOLUCIONES → BONOS_REGALO**
    - Cardinalidad: Una devolución genera un bono (1:1 en realidad, pero modelado como 1:N)
    - FK: bonos_regalo.devolucion_id → devoluciones.id

11. **CLIENTES → BONOS_REGALO**
    - Cardinalidad: Un cliente puede tener muchos bonos
    - FK: bonos_regalo.cliente_id → clientes.id

12. **POLITICAS_DATOS → USUARIOS**
    - Cardinalidad: Una política es aceptada por muchos usuarios
    - FK: usuarios.politica_id → politicas_datos.id

13. **POLITICAS_DATOS → CLIENTES**
    - Cardinalidad: Una política es aceptada por muchos clientes
    - FK: clientes.politica_id → politicas_datos.id

---

## 📐 NORMALIZACIÓN (3FN)

### Primera Forma Normal (1FN) ✅
- Todos los atributos contienen valores atómicos
- No hay grupos repetitivos
- Cada columna tiene un tipo de dato específico

### Segunda Forma Normal (2FN) ✅
- Cumple 1FN
- Todos los atributos no clave dependen completamente de la clave primaria
- No hay dependencias parciales

### Tercera Forma Normal (3FN) ✅
- Cumple 2FN
- No hay dependencias transitivas
- Ejemplo: `precio_unitario` en `detalle_venta` es necesario porque el precio del producto puede cambiar con el tiempo

---

## 🎯 CARDINALIDADES RESUMIDAS

```
PROVEEDORES    1 ─────< N    PRODUCTOS
USUARIOS       1 ─────< N    VENTAS
USUARIOS       1 ─────< N    MOVIMIENTOS_INVENTARIO
CLIENTES       0..1 ──< N    VENTAS
VENTAS         1 ─────< N    DETALLE_VENTA
PRODUCTOS      1 ─────< N    DETALLE_VENTA
PRODUCTOS      1 ─────< N    MOVIMIENTOS_INVENTARIO
VENTAS         1 ─────< N    DEVOLUCIONES
PRODUCTOS      1 ─────< N    DEVOLUCIONES
DEVOLUCIONES   1 ─────< 1    BONOS_REGALO
CLIENTES       1 ─────< N    BONOS_REGALO
POLITICAS      1 ─────< N    USUARIOS
POLITICAS      1 ─────< N    CLIENTES
```

---

## 🔐 RESTRICCIONES DE INTEGRIDAD

1. **DELETE CASCADE:** 
   - ventas → detalle_venta
   - devoluciones → bonos_regalo

2. **DELETE RESTRICT:**
   - proveedores → productos (no eliminar si tiene productos)
   - usuarios → ventas (no eliminar si tiene ventas)
   - clientes → ventas (no eliminar si tiene ventas)

3. **DELETE SET NULL:**
   - clientes → ventas (si se elimina cliente, venta permanece sin cliente)

4. **UNIQUE:**
   - usuarios.email
   - clientes.cedula
   - proveedores.nit
   - productos.codigo
   - bonos_regalo.codigo

---

## 📝 NOTAS IMPORTANTES

1. **Auditoría Automática:** Todos los cambios en tablas principales se registran automáticamente mediante triggers en la tabla `auditoria`.

2. **Inventario Automático:** Los cambios en `cantidad` de productos se registran automáticamente en `movimientos_inventario` mediante triggers.

3. **Bonos Automáticos:** Al insertar una devolución, se genera automáticamente un bono en `bonos_regalo` mediante trigger.

4. **Protección de Ventas:** Las ventas NO pueden eliminarse, solo cambiar estado a 'cancelada' mediante trigger BEFORE DELETE.

5. **Validación de Stock:** Antes de registrar una venta, se valida que haya stock suficiente mediante trigger BEFORE INSERT.

6. **Precios Históricos:** El `precio_unitario` se guarda en `detalle_venta` para mantener el precio al momento de la venta, aunque el precio del producto cambie después.
