<?php
namespace App\Models;

use App\Config\Database;

abstract class BaseModel {
    protected $pdo;
    protected $table;
    protected $primaryKey = 'id';

    public function __construct() {
        $this->pdo = Database::getInstance();
    }

    // Método genérico para crear
    public function create($data) {
        $fields = implode(', ', array_keys($data));
        $placeholders = ':' . implode(', :', array_keys($data));
        
        $sql = "INSERT INTO {$this->table} ($fields) VALUES ($placeholders)";
        $stmt = $this->pdo->prepare($sql);
        
        foreach ($data as $key => $value) {
            $stmt->bindValue(":$key", $value);
        }
        
        $stmt->execute();
        return $this->pdo->lastInsertId();
    }

    // Método genérico para actualizar
    public function update($id, $data) {
        $setClause = [];
        foreach ($data as $key => $value) {
            $setClause[] = "$key = :$key";
        }
        
        $sql = "UPDATE {$this->table} SET " . implode(', ', $setClause) . 
               " WHERE {$this->primaryKey} = :id";
        
        $stmt = $this->pdo->prepare($sql);
        $stmt->bindValue(':id', $id);
        
        foreach ($data as $key => $value) {
            $stmt->bindValue(":$key", $value);
        }
        
        return $stmt->execute();
    }

    // Obtener por ID
    public function find($id) {
        $sql = "SELECT * FROM {$this->table} WHERE {$this->primaryKey} = ?";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([$id]);
        return $stmt->fetch();
    }

    // Obtener todos (con paginación)
    public function all($limit = 100, $offset = 0) {
        $sql = "SELECT * FROM {$this->table} LIMIT ?, ?";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([$offset, $limit]);
        return $stmt->fetchAll();
    }

    // Soft delete (borrado lógico)
    public function delete($id) {
        $sql = "UPDATE {$this->table} SET estado = 'inactivo' WHERE {$this->primaryKey} = ?";
        $stmt = $this->pdo->prepare($sql);
        return $stmt->execute([$id]);
    }
}