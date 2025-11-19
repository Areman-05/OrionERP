<?php

namespace OrionERP\Models;

use OrionERP\Core\Database;

class Cliente
{
    private $db;

    public function __construct()
    {
        $this->db = Database::getInstance();
    }

    public function getAll(): array
    {
        return $this->db->fetchAll(
            "SELECT * FROM clientes ORDER BY nombre ASC"
        );
    }

    public function findById(int $id): ?array
    {
        return $this->db->fetchOne(
            "SELECT * FROM clientes WHERE id = ?",
            [$id]
        );
    }

    public function create(array $data): int
    {
        $sql = "INSERT INTO clientes (codigo, nombre, tipo_documento, numero_documento, email, telefono, direccion, ciudad, codigo_postal, estado) 
                VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";
        
        $this->db->query($sql, [
            $data['codigo'],
            $data['nombre'],
            $data['tipo_documento'] ?? 'DNI',
            $data['numero_documento'] ?? null,
            $data['email'] ?? null,
            $data['telefono'] ?? null,
            $data['direccion'] ?? null,
            $data['ciudad'] ?? null,
            $data['codigo_postal'] ?? null,
            $data['estado'] ?? 'activo'
        ]);

        return (int) $this->db->lastInsertId();
    }

    public function update(int $id, array $data): bool
    {
        $sql = "UPDATE clientes SET nombre = ?, email = ?, telefono = ?, direccion = ?, ciudad = ?, estado = ? WHERE id = ?";
        
        $this->db->query($sql, [
            $data['nombre'],
            $data['email'] ?? null,
            $data['telefono'] ?? null,
            $data['direccion'] ?? null,
            $data['ciudad'] ?? null,
            $data['estado'] ?? 'activo',
            $id
        ]);

        return true;
    }

    public function delete(int $id): bool
    {
        $this->db->query("UPDATE clientes SET estado = 'inactivo' WHERE id = ?", [$id]);
        return true;
    }
}

