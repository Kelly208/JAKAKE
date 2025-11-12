<?php
namespace App\Models;

class Producto extends BaseModel {
    protected $table = 'productos';
    protected $primaryKey = 'producto_id';

    // Buscar productos con stock y proveedores
    public function search($term) {
        $sql = "SELECT p.*, c.nombre_categoria 
                FROM productos p
                LEFT JOIN categorias_productos c ON p.categoria_id = c.categoria_id
                WHERE p.estado = 'activo' 
                AND (p.nombre LIKE ? OR p.codigo_sku LIKE ?)
                AND p.stock_actual > 0";
        $stmt = $this->pdo->prepare($sql);
        $searchTerm = "%$term%";
        $stmt->execute([$searchTerm, $searchTerm]);
        return $stmt->fetchAll();
    }

    // Actualizar stock con auditoría
    public function actualizarStock($producto_id, $cantidad, $tipo, $usuario_id, $referencia_id = null) {
        // Iniciar transacción
        $this->pdo->beginTransaction();
        
        try {
            // Obtener stock actual
            $sql = "SELECT stock_actual FROM productos WHERE producto_id = ?";
            $stmt = $this->pdo->prepare($sql);
            $stmt->execute([$producto_id]);
            $stock = $stmt->fetch();
            
            if (!$stock) {
                throw new \Exception("Producto no encontrado");
            }
            
            $stock_anterior = $stock['stock_actual'];
            $nuevo_stock = $tipo === 'entrada' ? $stock_anterior + $cantidad : $stock_anterior - $cantidad;
            
            // Actualizar producto
            $sql = "UPDATE productos SET stock_actual = ? WHERE producto_id = ?";
            $stmt = $this->pdo->prepare($sql);
            $stmt->execute([$nuevo_stock, $producto_id]);
            
            // Registrar movimiento
            $sql = "INSERT INTO movimientos_inventario 
                    (producto_id, tipo_movimiento, cantidad, stock_anterior, stock_nuevo, referencia_id, usuario_id) 
                    VALUES (?, ?, ?, ?, ?, ?, ?)";
            $stmt = $this->pdo->prepare($sql);
            $stmt->execute([
                $producto_id,
                $tipo,
                $cantidad,
                $stock_anterior,
                $nuevo_stock,
                $referencia_id,
                $usuario_id
            ]);
            
            $this->pdo->commit();
            return true;
            
        } catch (\Exception $e) {
            $this->pdo->rollBack();
            error_log("Error en actualizarStock: " . $e->getMessage());
            return false;
        }
    }
}