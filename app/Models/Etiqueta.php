<?php

namespace OrionERP\Models;

use OrionERP\Core\Database;

class Etiqueta
{
    private $db;

    public function __construct()
    {
        $this->db = Database::getInstance();
    }

    public function getAll(): array
    {
        return $this->db->fetchAll(
            "SELECT * FROM etiquetas WHERE activa = 1 ORDER BY nombre"
        );
    }

    public function findById(int $id): ?array
    {
        return $this->db->fetchOne(
            "SELECT * FROM etiquetas WHERE id = ?",
            [$id]
        );
    }

    public function getByProducto(int $productoId): array
    {
        return $this->db->fetchAll(
            "SELECT e.* FROM etiquetas e
             INNER JOIN producto_etiquetas pe ON e.id = pe.etiqueta_id
             WHERE pe.producto_id = ? AND e.activa = 1",
            [$productoId]
        );
    }

    public function create(array $data): int
    {
        $sql = "INSERT INTO etiquetas (nombre, color, activa) VALUES (?, ?, ?)";
        
        $this->db->query($sql, [
            $data['nombre'],
            $data['color'] ?? '#007bff',
            $data['activa'] ?? 1
        ]);

        return (int) $this->db->lastInsertId();
    }

    public function agregarAProducto(int $productoId, int $etiquetaId): bool
    {
        $existe = $this->db->fetchOne(
            "SELECT id FROM producto_etiquetas WHERE producto_id = ? AND etiqueta_id = ?",
            [$productoId, $etiquetaId]
        );

        if (!$existe) {
            $this->db->query(
                "INSERT INTO producto_etiquetas (producto_id, etiqueta_id) VALUES (?, ?)",
                [$productoId, $etiquetaId]
            );
        }

        return true;
    }

    public function quitarDeProducto(int $productoId, int $etiquetaId): bool
    {
        $this->db->query(
            "DELETE FROM producto_etiquetas WHERE producto_id = ? AND etiqueta_id = ?",
            [$productoId, $etiquetaId]
        );
        return true;
    }
}

