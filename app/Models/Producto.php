<?php

namespace OrionERP\Models;

use OrionERP\Core\Database;

class Producto
{
    private $db;

    public function __construct()
    {
        $this->db = Database::getInstance();
    }

    public function getAll(): array
    {
        return $this->db->fetchAll(
            "SELECT p.*, c.nombre as categoria_nombre 
             FROM productos p 
             LEFT JOIN categorias c ON p.categoria_id = c.id 
             ORDER BY p.nombre ASC"
        );
    }

    public function findById(int $id): ?array
    {
        return $this->db->fetchOne(
            "SELECT p.*, c.nombre as categoria_nombre 
             FROM productos p 
             LEFT JOIN categorias c ON p.categoria_id = c.id 
             WHERE p.id = ?",
            [$id]
        );
    }

    public function create(array $data): int
    {
        $sql = "INSERT INTO productos (codigo, nombre, descripcion, categoria_id, precio_venta, precio_compra, stock_actual, stock_minimo, activo) 
                VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)";
        
        $this->db->query($sql, [
            $data['codigo'],
            $data['nombre'],
            $data['descripcion'] ?? null,
            $data['categoria_id'] ?? null,
            $data['precio_venta'] ?? 0,
            $data['precio_compra'] ?? 0,
            $data['stock_actual'] ?? 0,
            $data['stock_minimo'] ?? 0,
            $data['activo'] ?? 1
        ]);

        return (int) $this->db->lastInsertId();
    }

    public function update(int $id, array $data): bool
    {
        $sql = "UPDATE productos SET nombre = ?, descripcion = ?, precio_venta = ?, stock_actual = ?, activo = ? WHERE id = ?";
        
        $this->db->query($sql, [
            $data['nombre'],
            $data['descripcion'] ?? null,
            $data['precio_venta'] ?? 0,
            $data['stock_actual'] ?? 0,
            $data['activo'] ?? 1,
            $id
        ]);

        return true;
    }
}

