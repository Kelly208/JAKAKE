<?php
namespace App\Models;

class ProveedorModel extends BaseModel {
    public function crear(array $data): bool {
        return $this->execute(
            "INSERT INTO proveedores(nombre, contacto, telefono) VALUES(?, ?, ?)",
            [$data['nombre'], $data['contacto'], $data['telefono']]
        );
    }
}
