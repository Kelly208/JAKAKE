-- Agregar columna medio_pago a tabla ventas
ALTER TABLE ventas 
ADD COLUMN medio_pago ENUM('efectivo', 'tarjeta', 'transferencia') DEFAULT 'efectivo' AFTER total;

-- Actualizar ventas existentes con valores aleatorios
UPDATE ventas SET medio_pago = 'efectivo' WHERE id % 3 = 0;
UPDATE ventas SET medio_pago = 'tarjeta' WHERE id % 3 = 1;
UPDATE ventas SET medio_pago = 'transferencia' WHERE id % 3 = 2;
