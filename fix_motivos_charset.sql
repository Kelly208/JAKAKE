-- Corregir motivos con caracteres mal codificados
UPDATE devoluciones 
SET motivo = 'Cliente compró de más'
WHERE motivo LIKE '%compr?? de m??s%';

-- Verificar si hay otros casos
SELECT id, motivo FROM devoluciones WHERE motivo LIKE '%??%';
