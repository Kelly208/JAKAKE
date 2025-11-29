-- Actualizar nombres de usuarios y clientes con tildes correctos
UPDATE usuarios SET nombre = 'María González' WHERE id = 1;
UPDATE usuarios SET nombre = 'Carlos Ramírez' WHERE id = 2;
UPDATE usuarios SET nombre = 'Laura Martínez' WHERE id = 3;
UPDATE usuarios SET nombre = 'Pedro Sánchez' WHERE id = 4;

UPDATE clientes SET nombre = 'Juan Pérez García' WHERE cedula = '1234567890';
UPDATE clientes SET nombre = 'Ana María López' WHERE cedula = '2345678901';
UPDATE clientes SET nombre = 'Roberto Castaño' WHERE cedula = '3456789012';
UPDATE clientes SET nombre = 'Sofía Hernández' WHERE cedula = '4567890123';
UPDATE clientes SET nombre = 'Diego Morales Sánchez' WHERE cedula = '5678901234';
UPDATE clientes SET nombre = 'Valentina Ruiz López' WHERE cedula = '6789012345';
UPDATE clientes SET nombre = 'Andrés Gómez' WHERE cedula = '7890123456';
UPDATE clientes SET nombre = 'Carolina Silva Martínez' WHERE cedula = '8901234567';
UPDATE clientes SET nombre = 'Miguel Ángel Torres' WHERE cedula = '9012345678';
UPDATE clientes SET nombre = 'Isabella Vargas García' WHERE cedula = '0123456789';

-- Actualizar proveedores
UPDATE proveedores SET nombre = 'Papelería Martínez' WHERE nit = '900123456-1';
UPDATE proveedores SET nombre = 'Útiles Bogotá' WHERE nit = '900234567-2';
UPDATE proveedores SET nombre = 'Distribuciones López' WHERE nit = '900345678-3';
UPDATE proveedores SET contacto = 'José Ramírez' WHERE nit = '900123456-1';
UPDATE proveedores SET contacto = 'María Rodríguez' WHERE nit = '900234567-2';
UPDATE proveedores SET contacto = 'Luis Sánchez' WHERE nit = '900345678-3';
