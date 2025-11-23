<?php

namespace OrionERP\Models;

use OrionERP\Core\Database;

class Proveedor
{
    private $db;

    public function __construct()
    {
        $this->db = Database::getInstance();
    }

    public function getAll(): array
    {
        return $this->db->fetchAll(
            "SELECT * FROM proveedores WHERE activo = 1 ORDER BY nombre ASC"
        );
    }

    public function findById(int $id): ?array
    {
        return $this->db->fetchOne(
            "SELECT * FROM proveedores WHERE id = ?",
            [$id]
        );
    }

    public function create(array $data): int
    {
        $sql = "INSERT INTO proveedores (codigo, nombre, cif, email, telefono, direccion, ciudad, codigo_postal, pais, activo, notas) 
                VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";
        
        $this->db->query($sql, [
            $data['codigo'],
            $data['nombre'],
            $data['cif'] ?? null,
            $data['email'] ?? null,
            $data['telefono'] ?? null,
            $data['direccion'] ?? null,
            $data['ciudad'] ?? null,
            $data['codigo_postal'] ?? null,
            $data['pais'] ?? 'España',
            $data['activo'] ?? 1,
            $data['notas'] ?? null
        ]);

        return (int) $this->db->lastInsertId();
    }

    public function update(int $id, array $data): bool
    {
        $sql = "UPDATE proveedores SET nombre = ?, cif = ?, email = ?, telefono = ?, direccion = ?, ciudad = ?, codigo_postal = ?, pais = ?, activo = ?, notas = ? WHERE id = ?";
        
        $this->db->query($sql, [
            $data['nombre'],
            $data['cif'] ?? null,
            $data['email'] ?? null,
            $data['telefono'] ?? null,
            $data['direccion'] ?? null,
            $data['ciudad'] ?? null,
            $data['codigo_postal'] ?? null,
            $data['pais'] ?? 'España',
            $data['activo'] ?? 1,
            $data['notas'] ?? null,
            $id
        ]);

        return true;
    }

    public function delete(int $id): bool
    {
        $this->db->query("UPDATE proveedores SET activo = 0 WHERE id = ?", [$id]);
        return true;
    }

    public function findByCodigo(string $codigo): ?array
    {
        return $this->db->fetchOne(
            "SELECT * FROM proveedores WHERE codigo = ?",
            [$codigo]
        );
    }
}

