<?php
namespace App\Models;

class ProductoModel extends BaseModel {
    protected  $table = 'productos';

    public function inventarioActual(): array {
        return $this->query("SELECT * FROM vw_inventario_actual");
    }

    public function crear(array $data): bool {
        return $this->execute(
            "INSERT INTO productos(nombre, precio, stock, proveedor_id) VALUES(?, ?, ?, ?)",
            [$data['nombre'], $data['precio'], $data['stock'], $data['proveedor_id']]
        );
    }

    public function productosMasVendidos(): array {
        return $this->query("SELECT * FROM vw_top_productos");
    }
}
