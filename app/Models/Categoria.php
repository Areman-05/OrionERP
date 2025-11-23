<?php

namespace OrionERP\Models;

use OrionERP\Core\Database;

class Categoria
{
    private $db;

    public function __construct()
    {
        $this->db = Database::getInstance();
    }

    public function getAll(): array
    {
        return $this->db->fetchAll(
            "SELECT c.*, 
                    (SELECT COUNT(*) FROM productos WHERE categoria_id = c.id AND activo = 1) as total_productos,
                    (SELECT COUNT(*) FROM categorias WHERE categoria_padre_id = c.id) as total_subcategorias
             FROM categorias c
             WHERE c.activa = 1
             ORDER BY c.nombre"
        );
    }

    public function getArbol(): array
    {
        $categorias = $this->getAll();
        return $this->construirArbol($categorias);
    }

    private function construirArbol(array $categorias, ?int $padreId = null): array
    {
        $arbol = [];
        
        foreach ($categorias as $categoria) {
            if (($categoria['categoria_padre_id'] ?? null) == $padreId) {
                $categoria['hijos'] = $this->construirArbol($categorias, $categoria['id']);
                $arbol[] = $categoria;
            }
        }
        
        return $arbol;
    }

    public function findById(int $id): ?array
    {
        return $this->db->fetchOne(
            "SELECT * FROM categorias WHERE id = ?",
            [$id]
        );
    }

    public function create(array $data): int
    {
        $sql = "INSERT INTO categorias (nombre, descripcion, categoria_padre_id, activa) 
                VALUES (?, ?, ?, ?)";
        
        $this->db->query($sql, [
            $data['nombre'],
            $data['descripcion'] ?? null,
            $data['categoria_padre_id'] ?? null,
            $data['activa'] ?? 1
        ]);

        return (int) $this->db->lastInsertId();
    }

    public function update(int $id, array $data): bool
    {
        $sql = "UPDATE categorias SET nombre = ?, descripcion = ?, categoria_padre_id = ?, activa = ? WHERE id = ?";
        
        $this->db->query($sql, [
            $data['nombre'],
            $data['descripcion'] ?? null,
            $data['categoria_padre_id'] ?? null,
            $data['activa'] ?? 1,
            $id
        ]);

        return true;
    }
}

