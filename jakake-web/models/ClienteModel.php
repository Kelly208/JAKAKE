<?php
namespace App\Models;

class ClienteModel extends BaseModel {
    public function crear(array $data): bool {
        return $this->execute(
            "INSERT INTO clientes(nombre, email, telefono) VALUES(?, ?, ?)",
            [$data['nombre'], $data['email'], $data['telefono']]
        );
    }

    public function listar(): array {
        return $this->query("SELECT * FROM clientes");
    }
}
