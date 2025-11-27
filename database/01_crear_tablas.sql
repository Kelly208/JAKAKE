-- =====================================================
-- SCRIPT DE CREACIÓN DE TABLAS
-- Sistema de Base de Datos - Papelería JAKAKE
-- =====================================================
-- Descripción: Creación de todas las tablas del sistema
--              con sus respectivas constraints
-- Autor: Persona 1
-- Fecha: 2025-11-26
-- =====================================================

-- Eliminar base de datos si existe y crear nueva
DROP DATABASE IF EXISTS papeleria_jakake;
CREATE DATABASE papeleria_jakake CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE papeleria_jakake;

-- =====================================================
-- TABLA: politicas_datos
-- Descripción: Almacena versiones de políticas de 
--              protección de datos personales
-- =====================================================
CREATE TABLE politicas_datos (
    id INT AUTO_INCREMENT PRIMARY KEY,
    version VARCHAR(20) NOT NULL UNIQUE,
    titulo VARCHAR(255) NOT NULL,
    contenido TEXT NOT NULL,
    fecha_vigencia DATE NOT NULL,
    activa BOOLEAN DEFAULT FALSE,
    fecha_creacion TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    
    INDEX idx_activa (activa),
    INDEX idx_fecha_vigencia (fecha_vigencia)
) ENGINE=InnoDB COMMENT='Políticas de protección de datos';

-- =====================================================
-- TABLA: usuarios
-- Descripción: Usuarios administrativos del sistema
--              (administradores y cajeros)
-- =====================================================
CREATE TABLE usuarios (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nombre VARCHAR(150) NOT NULL,
    email VARCHAR(150) NOT NULL UNIQUE,
    password_hash VARCHAR(255) NOT NULL,
    rol ENUM('administrador', 'cajero') NOT NULL,
    telefono VARCHAR(20),
    direccion VARCHAR(255),
    estado ENUM('activo', 'inactivo') DEFAULT 'activo',
    acepto_politicas BOOLEAN DEFAULT FALSE,
    politica_id INT,
    fecha_registro TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    fecha_actualizacion TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    
    FOREIGN KEY (politica_id) REFERENCES politicas_datos(id) ON DELETE RESTRICT,
    INDEX idx_email (email),
    INDEX idx_rol (rol),
    INDEX idx_estado (estado)
) ENGINE=InnoDB COMMENT='Usuarios administrativos del sistema';

-- =====================================================
-- TABLA: clientes
-- Descripción: Clientes del negocio sin autenticación
-- =====================================================
CREATE TABLE clientes (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nombre VARCHAR(150) NOT NULL,
    cedula VARCHAR(20) NOT NULL UNIQUE,
    telefono VARCHAR(20),
    direccion VARCHAR(255),
    email VARCHAR(150),
    fecha_nacimiento DATE,
    acepto_politicas BOOLEAN DEFAULT FALSE,
    politica_id INT,
    fecha_registro TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    fecha_actualizacion TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    
    FOREIGN KEY (politica_id) REFERENCES politicas_datos(id) ON DELETE RESTRICT,
    INDEX idx_cedula (cedula),
    INDEX idx_nombre (nombre),
    INDEX idx_email (email)
) ENGINE=InnoDB COMMENT='Clientes del negocio';

-- =====================================================
-- TABLA: proveedores
-- Descripción: Proveedores que suministran productos
-- =====================================================
CREATE TABLE proveedores (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nombre VARCHAR(150) NOT NULL,
    nit VARCHAR(20) NOT NULL UNIQUE,
    direccion VARCHAR(255),
    telefono VARCHAR(20),
    contacto VARCHAR(150) COMMENT 'Nombre persona de contacto',
    ciudad VARCHAR(100),
    email VARCHAR(150),
    estado ENUM('activo', 'inactivo') DEFAULT 'activo',
    fecha_registro TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    fecha_actualizacion TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    
    INDEX idx_nit (nit),
    INDEX idx_nombre (nombre),
    INDEX idx_estado (estado)
) ENGINE=InnoDB COMMENT='Proveedores de productos';

-- =====================================================
-- TABLA: productos
-- Descripción: Productos en inventario
-- =====================================================
CREATE TABLE productos (
    id INT AUTO_INCREMENT PRIMARY KEY,
    codigo VARCHAR(50) NOT NULL UNIQUE,
    nombre VARCHAR(200) NOT NULL,
    descripcion TEXT,
    precio DECIMAL(10, 2) NOT NULL CHECK (precio >= 0),
    cantidad INT NOT NULL DEFAULT 0 CHECK (cantidad >= 0),
    cantidad_minima INT DEFAULT 10 COMMENT 'Cantidad mínima para alertas',
    proveedor_id INT NOT NULL,
    tipo ENUM('escolar', 'oficina', 'arte', 'otro') DEFAULT 'otro',
    estado ENUM('activo', 'inactivo') DEFAULT 'activo',
    fecha_ingreso DATE DEFAULT (CURRENT_DATE),
    fecha_actualizacion TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    
    FOREIGN KEY (proveedor_id) REFERENCES proveedores(id) ON DELETE RESTRICT,
    INDEX idx_codigo (codigo),
    INDEX idx_nombre (nombre),
    INDEX idx_proveedor (proveedor_id),
    INDEX idx_tipo (tipo),
    INDEX idx_estado (estado),
    INDEX idx_cantidad (cantidad)
) ENGINE=InnoDB COMMENT='Productos en inventario';

-- =====================================================
-- TABLA: movimientos_inventario
-- Descripción: Historial de cambios en inventario
-- =====================================================
CREATE TABLE movimientos_inventario (
    id INT AUTO_INCREMENT PRIMARY KEY,
    producto_id INT NOT NULL,
    tipo_movimiento ENUM('entrada', 'salida', 'ajuste') NOT NULL,
    cantidad_anterior INT NOT NULL,
    cantidad_nueva INT NOT NULL,
    cantidad_movimiento INT NOT NULL COMMENT 'Cantidad del movimiento (+ o -)',
    motivo VARCHAR(255),
    usuario_id INT,
    fecha_movimiento TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    
    FOREIGN KEY (producto_id) REFERENCES productos(id) ON DELETE RESTRICT,
    FOREIGN KEY (usuario_id) REFERENCES usuarios(id) ON DELETE SET NULL,
    INDEX idx_producto (producto_id),
    INDEX idx_tipo (tipo_movimiento),
    INDEX idx_fecha (fecha_movimiento),
    INDEX idx_usuario (usuario_id)
) ENGINE=InnoDB COMMENT='Historial de movimientos de inventario';

-- =====================================================
-- TABLA: ventas
-- Descripción: Registro de ventas realizadas
-- =====================================================
CREATE TABLE ventas (
    id INT AUTO_INCREMENT PRIMARY KEY,
    fecha DATE NOT NULL DEFAULT (CURRENT_DATE),
    cliente_id INT NULL COMMENT 'NULL si es venta sin cliente registrado',
    usuario_id INT NOT NULL COMMENT 'Cajero que realizó la venta',
    subtotal DECIMAL(10, 2) NOT NULL CHECK (subtotal >= 0),
    impuesto DECIMAL(10, 2) NOT NULL CHECK (impuesto >= 0) COMMENT 'IVA 19%',
    total DECIMAL(10, 2) NOT NULL CHECK (total >= 0),
    estado ENUM('completada', 'cancelada') DEFAULT 'completada',
    fecha_registro TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    
    FOREIGN KEY (cliente_id) REFERENCES clientes(id) ON DELETE SET NULL,
    FOREIGN KEY (usuario_id) REFERENCES usuarios(id) ON DELETE RESTRICT,
    INDEX idx_fecha (fecha),
    INDEX idx_cliente (cliente_id),
    INDEX idx_usuario (usuario_id),
    INDEX idx_estado (estado),
    INDEX idx_fecha_registro (fecha_registro)
) ENGINE=InnoDB COMMENT='Registro de ventas';

-- =====================================================
-- TABLA: detalle_venta
-- Descripción: Productos vendidos en cada venta
-- =====================================================
CREATE TABLE detalle_venta (
    id INT AUTO_INCREMENT PRIMARY KEY,
    venta_id INT NOT NULL,
    producto_id INT NOT NULL,
    cantidad INT NOT NULL CHECK (cantidad > 0),
    precio_unitario DECIMAL(10, 2) NOT NULL CHECK (precio_unitario >= 0) COMMENT 'Precio al momento de la venta',
    subtotal DECIMAL(10, 2) NOT NULL CHECK (subtotal >= 0) COMMENT 'cantidad * precio_unitario',
    
    FOREIGN KEY (venta_id) REFERENCES ventas(id) ON DELETE CASCADE,
    FOREIGN KEY (producto_id) REFERENCES productos(id) ON DELETE RESTRICT,
    INDEX idx_venta (venta_id),
    INDEX idx_producto (producto_id)
) ENGINE=InnoDB COMMENT='Detalle de productos por venta';

-- =====================================================
-- TABLA: devoluciones
-- Descripción: Devoluciones de productos
-- =====================================================
CREATE TABLE devoluciones (
    id INT AUTO_INCREMENT PRIMARY KEY,
    venta_id INT NOT NULL,
    producto_id INT NOT NULL,
    cantidad INT NOT NULL CHECK (cantidad > 0),
    motivo TEXT NOT NULL,
    valor_devolucion DECIMAL(10, 2) NOT NULL CHECK (valor_devolucion >= 0),
    usuario_id INT NOT NULL COMMENT 'Usuario que procesa la devolución',
    fecha_devolucion DATE DEFAULT (CURRENT_DATE),
    fecha_registro TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    
    FOREIGN KEY (venta_id) REFERENCES ventas(id) ON DELETE RESTRICT,
    FOREIGN KEY (producto_id) REFERENCES productos(id) ON DELETE RESTRICT,
    FOREIGN KEY (usuario_id) REFERENCES usuarios(id) ON DELETE RESTRICT,
    INDEX idx_venta (venta_id),
    INDEX idx_producto (producto_id),
    INDEX idx_fecha (fecha_devolucion),
    INDEX idx_usuario (usuario_id)
) ENGINE=InnoDB COMMENT='Devoluciones de productos';

-- =====================================================
-- TABLA: bonos_regalo
-- Descripción: Bonos generados por devoluciones
-- =====================================================
CREATE TABLE bonos_regalo (
    id INT AUTO_INCREMENT PRIMARY KEY,
    codigo VARCHAR(50) NOT NULL UNIQUE COMMENT 'Código único del bono',
    devolucion_id INT NOT NULL,
    cliente_id INT NOT NULL,
    valor DECIMAL(10, 2) NOT NULL CHECK (valor > 0),
    estado ENUM('activo', 'usado', 'vencido') DEFAULT 'activo',
    fecha_emision DATE DEFAULT (CURRENT_DATE),
    fecha_vencimiento DATE NOT NULL,
    fecha_uso DATE NULL,
    venta_uso_id INT NULL COMMENT 'Venta donde se usó el bono',
    
    FOREIGN KEY (devolucion_id) REFERENCES devoluciones(id) ON DELETE CASCADE,
    FOREIGN KEY (cliente_id) REFERENCES clientes(id) ON DELETE RESTRICT,
    FOREIGN KEY (venta_uso_id) REFERENCES ventas(id) ON DELETE SET NULL,
    INDEX idx_codigo (codigo),
    INDEX idx_estado (estado),
    INDEX idx_cliente (cliente_id),
    INDEX idx_fecha_vencimiento (fecha_vencimiento)
) ENGINE=InnoDB COMMENT='Bonos de regalo por devoluciones';

-- =====================================================
-- TABLA: aceptacion_politicas
-- Descripción: Registro de aceptación de políticas
-- =====================================================
CREATE TABLE aceptacion_politicas (
    id INT AUTO_INCREMENT PRIMARY KEY,
    politica_id INT NOT NULL,
    tipo_usuario ENUM('usuario', 'cliente') NOT NULL,
    usuario_id INT NULL,
    cliente_id INT NULL,
    fecha_aceptacion TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    ip_aceptacion VARCHAR(45),
    
    FOREIGN KEY (politica_id) REFERENCES politicas_datos(id) ON DELETE RESTRICT,
    FOREIGN KEY (usuario_id) REFERENCES usuarios(id) ON DELETE CASCADE,
    FOREIGN KEY (cliente_id) REFERENCES clientes(id) ON DELETE CASCADE,
    CHECK (
        (tipo_usuario = 'usuario' AND usuario_id IS NOT NULL AND cliente_id IS NULL) OR
        (tipo_usuario = 'cliente' AND cliente_id IS NOT NULL AND usuario_id IS NULL)
    ),
    INDEX idx_politica (politica_id),
    INDEX idx_tipo (tipo_usuario),
    INDEX idx_usuario (usuario_id),
    INDEX idx_cliente (cliente_id)
) ENGINE=InnoDB COMMENT='Registro de aceptación de políticas';

-- =====================================================
-- TABLA: auditoria
-- Descripción: Registro automático de operaciones CRUD
-- =====================================================
CREATE TABLE auditoria (
    id INT AUTO_INCREMENT PRIMARY KEY,
    tabla_afectada VARCHAR(100) NOT NULL,
    operacion ENUM('INSERT', 'UPDATE', 'DELETE') NOT NULL,
    registro_id INT NOT NULL COMMENT 'ID del registro afectado',
    usuario_id INT NULL COMMENT 'NULL si es operación del sistema',
    valores_anteriores JSON NULL COMMENT 'Valores antes del cambio',
    valores_nuevos JSON NULL COMMENT 'Valores después del cambio',
    fecha_hora TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    ip_usuario VARCHAR(45),
    
    FOREIGN KEY (usuario_id) REFERENCES usuarios(id) ON DELETE SET NULL,
    INDEX idx_tabla (tabla_afectada),
    INDEX idx_operacion (operacion),
    INDEX idx_fecha (fecha_hora),
    INDEX idx_usuario (usuario_id),
    INDEX idx_registro (registro_id)
) ENGINE=InnoDB COMMENT='Auditoría de operaciones del sistema';

-- =====================================================
-- FINALIZACIÓN
-- =====================================================

-- Mostrar tablas creadas
SHOW TABLES;

-- Mensaje de confirmación
SELECT 'Base de datos papeleria_jakake creada exitosamente con 12 tablas' AS mensaje;
