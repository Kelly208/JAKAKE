# DICCIONARIO DE DATOS
## Base de Datos Papelería JAKAKE

---

## 📋 TABLA: politicas_datos

**Descripción:** Almacena las diferentes versiones de las políticas de protección de datos personales según la Ley 1581 de 2012.

| Campo | Tipo | Restricciones | Descripción | Ejemplo |
|-------|------|---------------|-------------|---------|
| id | INT | PK, AUTO_INCREMENT | Identificador único de la política | 1 |
| version | VARCHAR(20) | NOT NULL | Número de versión de la política | "1.0" |
| titulo | VARCHAR(200) | NOT NULL | Título de la política | "Política de Tratamiento de Datos" |
| contenido | TEXT | NOT NULL | Texto completo de la política | "En cumplimiento..." |
| fecha_vigencia | DATE | NOT NULL | Fecha desde que aplica | 2025-01-01 |
| activa | BOOLEAN | DEFAULT TRUE | Si la política está vigente | TRUE |

**Relaciones:**
- 1:N con `usuarios` (una política puede ser aceptada por muchos usuarios)
- 1:N con `clientes` (una política puede ser aceptada por muchos clientes)

---

## 📋 TABLA: usuarios

**Descripción:** Usuarios administrativos que tienen acceso al sistema (administradores y cajeros).

| Campo | Tipo | Restricciones | Descripción | Ejemplo |
|-------|------|---------------|-------------|---------|
| id | INT | PK, AUTO_INCREMENT | Identificador único del usuario | 1 |
| nombre | VARCHAR(100) | NOT NULL | Nombre completo | "Juan Pérez" |
| email | VARCHAR(100) | NOT NULL, UNIQUE | Correo electrónico | "juan@jakake.com" |
| password_hash | VARCHAR(255) | NOT NULL | Contraseña encriptada (bcrypt) | "$2y$10$..." |
| rol | ENUM | 'administrador', 'cajero' | Rol en el sistema | "cajero" |
| telefono | VARCHAR(20) | NULL | Teléfono de contacto | "3001234567" |
| direccion | VARCHAR(200) | NULL | Dirección de residencia | "Calle 10 #20-30" |
| estado | ENUM | DEFAULT 'activo' | Estado del usuario | "activo" |
| fecha_registro | TIMESTAMP | DEFAULT CURRENT_TIMESTAMP | Fecha de creación | 2025-01-15 10:30:00 |
| acepto_politicas | BOOLEAN | DEFAULT FALSE | Si aceptó políticas | TRUE |
| politica_id | INT | FK → politicas_datos | ID de política aceptada | 1 |

**Relaciones:**
- N:1 con `politicas_datos`
- 1:N con `ventas` (un usuario registra muchas ventas)
- 1:N con `movimientos_inventario`
- 1:N con `auditoria`

**Índices:**
- PRIMARY KEY (id)
- UNIQUE KEY (email)
- INDEX (politica_id)

---

## 📋 TABLA: clientes

**Descripción:** Clientes del negocio que realizan compras. No tienen acceso al sistema.

| Campo | Tipo | Restricciones | Descripción | Ejemplo |
|-------|------|---------------|-------------|---------|
| id | INT | PK, AUTO_INCREMENT | Identificador único del cliente | 1 |
| nombre | VARCHAR(100) | NOT NULL | Nombre completo | "María González" |
| cedula | VARCHAR(20) | NOT NULL, UNIQUE | Número de identificación | "1234567890" |
| telefono | VARCHAR(20) | NULL | Teléfono de contacto | "3109876543" |
| direccion | VARCHAR(200) | NULL | Dirección de residencia | "Carrera 5 #15-20" |
| email | VARCHAR(100) | NULL | Correo electrónico | "maria@gmail.com" |
| fecha_nacimiento | DATE | NULL | Fecha de nacimiento | 1990-05-15 |
| fecha_registro | TIMESTAMP | DEFAULT CURRENT_TIMESTAMP | Fecha de registro | 2025-02-01 14:20:00 |
| acepto_politicas | BOOLEAN | DEFAULT FALSE | Si aceptó políticas | TRUE |
| politica_id | INT | FK → politicas_datos | ID de política aceptada | 1 |

**Relaciones:**
- N:1 con `politicas_datos`
- 1:N con `ventas` (un cliente tiene muchas compras)
- 1:N con `bonos_regalo`

**Índices:**
- PRIMARY KEY (id)
- UNIQUE KEY (cedula)
- INDEX (politica_id)
- FULLTEXT INDEX (nombre, email)

---

## 📋 TABLA: proveedores

**Descripción:** Proveedores que suministran productos a la papelería.

| Campo | Tipo | Restricciones | Descripción | Ejemplo |
|-------|------|---------------|-------------|---------|
| id | INT | PK, AUTO_INCREMENT | Identificador único del proveedor | 1 |
| nombre | VARCHAR(100) | NOT NULL | Nombre o razón social | "Distribuidora XYZ" |
| nit | VARCHAR(20) | NOT NULL, UNIQUE | NIT del proveedor | "900123456-7" |
| direccion | VARCHAR(200) | NULL | Dirección física | "Calle 50 #30-40" |
| telefono | VARCHAR(20) | NULL | Teléfono principal | "6015551234" |
| contacto | VARCHAR(100) | NULL | Persona de contacto | "Pedro Ruiz" |
| ciudad | VARCHAR(50) | NULL | Ciudad de operación | "Bogotá" |
| email | VARCHAR(100) | NULL | Correo electrónico | "ventas@distribuidora.com" |
| estado | ENUM | DEFAULT 'activo' | Estado del proveedor | "activo" |
| fecha_registro | TIMESTAMP | DEFAULT CURRENT_TIMESTAMP | Fecha de registro | 2025-01-10 09:00:00 |

**Relaciones:**
- 1:N con `productos` (un proveedor suministra muchos productos)

**Índices:**
- PRIMARY KEY (id)
- UNIQUE KEY (nit)
- FULLTEXT INDEX (nombre, contacto)

---

## 📋 TABLA: productos

**Descripción:** Catálogo de productos disponibles en el inventario.

| Campo | Tipo | Restricciones | Descripción | Ejemplo |
|-------|------|---------------|-------------|---------|
| id | INT | PK, AUTO_INCREMENT | Identificador único del producto | 1 |
| codigo | VARCHAR(50) | NOT NULL, UNIQUE | Código del producto | "LAP-001" |
| nombre | VARCHAR(100) | NOT NULL | Nombre del producto | "Lápiz HB" |
| descripcion | TEXT | NULL | Descripción detallada | "Lápiz de grafito HB" |
| precio | DECIMAL(10,2) | NOT NULL, CHECK > 0 | Precio de venta | 1500.00 |
| cantidad | INT | NOT NULL, DEFAULT 0 | Cantidad en stock | 50 |
| cantidad_minima | INT | DEFAULT 10 | Stock mínimo para alerta | 10 |
| proveedor_id | INT | FK → proveedores | Proveedor del producto | 1 |
| tipo | ENUM | 'escolar', 'oficina', 'arte', 'otro' | Categoría | "escolar" |
| estado | ENUM | DEFAULT 'activo' | Estado del producto | "activo" |
| fecha_ingreso | TIMESTAMP | DEFAULT CURRENT_TIMESTAMP | Fecha de creación | 2025-01-15 11:00:00 |
| fecha_actualizacion | TIMESTAMP | ON UPDATE CURRENT_TIMESTAMP | Última actualización | 2025-02-10 16:30:00 |

**Relaciones:**
- N:1 con `proveedores`
- 1:N con `detalle_venta`
- 1:N con `movimientos_inventario`
- 1:N con `devoluciones`

**Índices:**
- PRIMARY KEY (id)
- UNIQUE KEY (codigo)
- INDEX (proveedor_id)
- INDEX idx_productos_proveedor_estado (proveedor_id, estado)
- FULLTEXT INDEX (nombre, descripcion)

---

## 📋 TABLA: movimientos_inventario

**Descripción:** Historial completo de movimientos de inventario (entradas, salidas, ajustes).

| Campo | Tipo | Restricciones | Descripción | Ejemplo |
|-------|------|---------------|-------------|---------|
| id | INT | PK, AUTO_INCREMENT | Identificador único del movimiento | 1 |
| producto_id | INT | FK → productos | Producto afectado | 5 |
| tipo_movimiento | ENUM | 'entrada', 'salida', 'ajuste' | Tipo de movimiento | "salida" |
| cantidad_anterior | INT | NOT NULL | Stock antes del movimiento | 50 |
| cantidad_nueva | INT | NOT NULL | Stock después del movimiento | 48 |
| cantidad_movimiento | INT | NOT NULL | Cantidad del movimiento | 2 |
| motivo | VARCHAR(200) | NULL | Razón del movimiento | "Venta ID 123" |
| usuario_id | INT | FK → usuarios, NULL | Usuario que realizó el movimiento | 3 |
| fecha_movimiento | TIMESTAMP | DEFAULT CURRENT_TIMESTAMP | Fecha del movimiento | 2025-02-15 10:45:00 |

**Relaciones:**
- N:1 con `productos`
- N:1 con `usuarios`

**Índices:**
- PRIMARY KEY (id)
- INDEX (producto_id)
- INDEX (usuario_id)
- INDEX idx_movimientos_producto_fecha (producto_id, fecha_movimiento)

---

## 📋 TABLA: ventas

**Descripción:** Registro de todas las ventas realizadas en el sistema.

| Campo | Tipo | Restricciones | Descripción | Ejemplo |
|-------|------|---------------|-------------|---------|
| id | INT | PK, AUTO_INCREMENT | Identificador único de la venta | 1 |
| fecha | DATE | NOT NULL | Fecha de la venta | 2025-02-15 |
| cliente_id | INT | FK → clientes, NULL | Cliente (NULL si no registrado) | 5 |
| usuario_id | INT | FK → usuarios | Cajero que realizó la venta | 3 |
| subtotal | DECIMAL(10,2) | NOT NULL, CHECK >= 0 | Subtotal sin impuestos | 10000.00 |
| impuesto | DECIMAL(10,2) | NOT NULL, CHECK >= 0 | IVA (19%) | 1900.00 |
| total | DECIMAL(10,2) | NOT NULL, CHECK >= 0 | Total de la venta | 11900.00 |
| estado | ENUM | DEFAULT 'completada' | Estado de la venta | "completada" |
| fecha_registro | TIMESTAMP | DEFAULT CURRENT_TIMESTAMP | Timestamp del registro | 2025-02-15 14:30:00 |

**Relaciones:**
- N:1 con `clientes` (puede ser NULL)
- N:1 con `usuarios`
- 1:N con `detalle_venta`
- 1:N con `devoluciones`
- 1:N con `bonos_regalo` (cuando se usa)

**Índices:**
- PRIMARY KEY (id)
- INDEX (cliente_id)
- INDEX (usuario_id)
- INDEX idx_ventas_fecha_cliente (fecha, cliente_id)
- INDEX idx_ventas_fecha_estado (fecha, estado)

**Restricciones especiales:**
- Las ventas NO pueden eliminarse (trigger `tr_prevenir_eliminar_ventas`)
- Solo pueden cambiar estado a "cancelada"

---

## 📋 TABLA: detalle_venta

**Descripción:** Detalle de los productos vendidos en cada venta.

| Campo | Tipo | Restricciones | Descripción | Ejemplo |
|-------|------|---------------|-------------|---------|
| id | INT | PK, AUTO_INCREMENT | Identificador único del detalle | 1 |
| venta_id | INT | FK → ventas, CASCADE | Venta asociada | 10 |
| producto_id | INT | FK → productos | Producto vendido | 5 |
| cantidad | INT | NOT NULL, CHECK > 0 | Cantidad vendida | 2 |
| precio_unitario | DECIMAL(10,2) | NOT NULL, CHECK > 0 | Precio al momento de venta | 4500.00 |
| subtotal | DECIMAL(10,2) | NOT NULL, CHECK >= 0 | cantidad * precio_unitario | 9000.00 |

**Relaciones:**
- N:1 con `ventas` (ON DELETE CASCADE)
- N:1 con `productos`

**Índices:**
- PRIMARY KEY (id)
- INDEX (venta_id)
- INDEX (producto_id)

**Notas:**
- `precio_unitario` se guarda para mantener histórico (el precio del producto puede cambiar)
- Al insertar, se actualiza automáticamente el stock mediante trigger

---

## 📋 TABLA: devoluciones

**Descripción:** Registro de devoluciones de productos vendidos.

| Campo | Tipo | Restricciones | Descripción | Ejemplo |
|-------|------|---------------|-------------|---------|
| id | INT | PK, AUTO_INCREMENT | Identificador único de devolución | 1 |
| venta_id | INT | FK → ventas | Venta original | 10 |
| producto_id | INT | FK → productos | Producto devuelto | 5 |
| cantidad | INT | NOT NULL, CHECK > 0 | Cantidad devuelta | 1 |
| motivo | TEXT | NULL | Razón de la devolución | "Producto defectuoso" |
| valor_devolucion | DECIMAL(10,2) | NOT NULL, CHECK > 0 | Valor del producto | 4500.00 |
| usuario_id | INT | FK → usuarios | Usuario que procesa | 3 |
| fecha_devolucion | DATE | NOT NULL | Fecha de la devolución | 2025-02-20 |
| fecha_registro | TIMESTAMP | DEFAULT CURRENT_TIMESTAMP | Timestamp del registro | 2025-02-20 16:45:00 |

**Relaciones:**
- N:1 con `ventas`
- N:1 con `productos`
- N:1 con `usuarios`
- 1:1 con `bonos_regalo` (genera automáticamente)

**Índices:**
- PRIMARY KEY (id)
- INDEX (venta_id)
- INDEX (producto_id)
- INDEX (usuario_id)

**Triggers asociados:**
- Al insertar: genera automáticamente un bono de regalo
- Al insertar: valida que el producto fue vendido en esa venta
- Al insertar: incrementa stock del producto

---

## 📋 TABLA: bonos_regalo

**Descripción:** Bonos de regalo generados automáticamente por devoluciones.

| Campo | Tipo | Restricciones | Descripción | Ejemplo |
|-------|------|---------------|-------------|---------|
| id | INT | PK, AUTO_INCREMENT | Identificador único del bono | 1 |
| codigo | VARCHAR(50) | NOT NULL, UNIQUE | Código único del bono | "BONO-20250220-1" |
| devolucion_id | INT | FK → devoluciones, CASCADE | Devolución que generó el bono | 5 |
| cliente_id | INT | FK → clientes | Cliente propietario | 10 |
| valor | DECIMAL(10,2) | NOT NULL, CHECK > 0 | Valor del bono | 4500.00 |
| estado | ENUM | DEFAULT 'activo' | Estado actual | "activo" |
| fecha_emision | DATE | NOT NULL | Fecha de creación | 2025-02-20 |
| fecha_vencimiento | DATE | NOT NULL | Fecha de expiración (90 días) | 2025-05-21 |
| fecha_uso | DATE | NULL | Fecha en que se usó | NULL |
| venta_uso_id | INT | FK → ventas, NULL | Venta donde se aplicó | NULL |

**Relaciones:**
- N:1 con `devoluciones` (ON DELETE CASCADE)
- N:1 con `clientes`
- N:1 con `ventas` (cuando se usa)

**Índices:**
- PRIMARY KEY (id)
- UNIQUE KEY (codigo)
- INDEX (cliente_id)
- INDEX (devolucion_id)
- INDEX idx_bonos_cliente_estado (cliente_id, estado)

**Triggers asociados:**
- Se genera automáticamente al insertar una devolución
- Se marca "vencido" diariamente por evento `evt_marcar_bonos_vencidos`

---

## 📋 TABLA: aceptacion_politicas

**Descripción:** Registro de aceptación de políticas de protección de datos.

| Campo | Tipo | Restricciones | Descripción | Ejemplo |
|-------|------|---------------|-------------|---------|
| id | INT | PK, AUTO_INCREMENT | Identificador único | 1 |
| politica_id | INT | FK → politicas_datos | Política aceptada | 1 |
| tipo_usuario | ENUM | 'usuario', 'cliente' | Tipo de persona | "cliente" |
| usuario_id | INT | FK → usuarios, NULL | ID usuario (si aplica) | NULL |
| cliente_id | INT | FK → clientes, NULL | ID cliente (si aplica) | 10 |
| fecha_aceptacion | TIMESTAMP | DEFAULT CURRENT_TIMESTAMP | Cuándo aceptó | 2025-02-01 10:00:00 |
| ip_aceptacion | VARCHAR(45) | NULL | IP desde donde aceptó | "192.168.1.100" |

**Relaciones:**
- N:1 con `politicas_datos`
- N:1 con `usuarios` (si tipo_usuario = 'usuario')
- N:1 con `clientes` (si tipo_usuario = 'cliente')

**Índices:**
- PRIMARY KEY (id)
- INDEX (politica_id)
- INDEX (usuario_id)
- INDEX (cliente_id)

**Restricciones:**
- `usuario_id` es NULL si `tipo_usuario` = 'cliente'
- `cliente_id` es NULL si `tipo_usuario` = 'usuario'

---

## 📋 TABLA: auditoria

**Descripción:** Registro automático de todas las operaciones CRUD en el sistema.

| Campo | Tipo | Restricciones | Descripción | Ejemplo |
|-------|------|---------------|-------------|---------|
| id | INT | PK, AUTO_INCREMENT | Identificador único | 1 |
| tabla_afectada | VARCHAR(50) | NOT NULL | Nombre de la tabla | "productos" |
| operacion | ENUM | 'INSERT', 'UPDATE', 'DELETE' | Tipo de operación | "UPDATE" |
| registro_id | INT | NOT NULL | ID del registro afectado | 5 |
| usuario_id | INT | FK → usuarios, NULL | Usuario que realizó acción | 3 |
| valores_anteriores | JSON | NULL | Valores antes del cambio | {"precio": 1000} |
| valores_nuevos | JSON | NULL | Valores después del cambio | {"precio": 1500} |
| fecha_hora | TIMESTAMP | DEFAULT CURRENT_TIMESTAMP | Timestamp de la operación | 2025-02-15 14:30:00 |
| ip_usuario | VARCHAR(45) | NULL | IP del usuario | "192.168.1.50" |

**Relaciones:**
- N:1 con `usuarios` (puede ser NULL para operaciones del sistema)

**Índices:**
- PRIMARY KEY (id)
- INDEX (usuario_id)
- INDEX idx_auditoria_tabla_fecha (tabla_afectada, fecha_hora)

**Notas:**
- Se llena automáticamente mediante 18 triggers
- No debe editarse manualmente
- `valores_anteriores` es NULL en operaciones INSERT
- `valores_nuevos` es NULL en operaciones DELETE

---

## 📊 RESUMEN DE TIPOS DE DATOS

### Tipos Numéricos
- **INT**: Identificadores, cantidades, contadores
- **DECIMAL(10,2)**: Precios, valores monetarios (precisión exacta)

### Tipos de Texto
- **VARCHAR(20-255)**: Textos cortos con longitud variable
- **TEXT**: Contenidos largos (descripciones, motivos, políticas)
- **ENUM**: Valores predefinidos (estado, rol, tipo_movimiento)
- **JSON**: Datos estructurados (valores de auditoría)

### Tipos de Fecha/Hora
- **DATE**: Fechas sin hora (fecha_nacimiento, fecha_vencimiento)
- **TIMESTAMP**: Fecha y hora con actualizaciones automáticas
- **DEFAULT CURRENT_TIMESTAMP**: Se establece automáticamente al insertar
- **ON UPDATE CURRENT_TIMESTAMP**: Se actualiza automáticamente al modificar

### Tipos Especiales
- **BOOLEAN**: TRUE/FALSE (activa, acepto_politicas)

---

## 🔒 RESTRICCIONES GLOBALES

### CHECK Constraints
- Precios y valores siempre positivos (> 0)
- Cantidades siempre positivas (> 0 o >= 0)
- Totales consistentes con subtotales e impuestos

### UNIQUE Constraints
- emails de usuarios
- cédulas de clientes
- NITs de proveedores
- códigos de productos
- códigos de bonos

### Foreign Keys
- ON DELETE CASCADE: detalle_venta, bonos_regalo
- ON DELETE RESTRICT: productos, ventas (con clientes/usuarios)
- ON DELETE SET NULL: ventas (con clientes)

---

## 📝 CONVENCIONES DE NOMENCLATURA

- **Tablas**: minúsculas, plural, guiones bajos
- **Columnas**: minúsculas, guiones bajos
- **Primary Keys**: siempre "id"
- **Foreign Keys**: nombre_tabla_id
- **Timestamps**: fecha_* para DATE, fecha_hora para TIMESTAMP
- **Estados**: ENUM con valores descriptivos en español
- **Índices**: idx_tabla_campos

---

**Fecha de creación:** Noviembre 27, 2025  
**Autores:** Kelly Palacio, Juan Esteban Albarán  
**Versión:** 1.0
