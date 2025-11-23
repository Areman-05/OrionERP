<?php

namespace OrionERP\Models;

use OrionERP\Core\Database;

class HistoricoProducto
{
    private $db;

    public function __construct()
    {
        $this->db = Database::getInstance();
    }

    public function getByProducto(int $productoId): array
    {
        return $this->db->fetchAll(
            "SELECT h.*, u.nombre as usuario_nombre 
             FROM historico_productos h
             LEFT JOIN usuarios u ON h.usuario_id = u.id
             WHERE h.producto_id = ?
             ORDER BY h.created_at DESC",
            [$productoId]
        );
    }

    public function registrarCambio(int $productoId, string $campo, $valorAnterior, $valorNuevo, ?int $usuarioId = null, ?string $motivo = null): int
    {
        $sql = "INSERT INTO historico_productos (producto_id, usuario_id, campo, valor_anterior, valor_nuevo, motivo) 
                VALUES (?, ?, ?, ?, ?, ?)";
        
        $this->db->query($sql, [
            $productoId,
            $usuarioId,
            $campo,
            is_array($valorAnterior) ? json_encode($valorAnterior) : $valorAnterior,
            is_array($valorNuevo) ? json_encode($valorNuevo) : $valorNuevo,
            $motivo
        ]);

        return (int) $this->db->lastInsertId();
    }

    public function getAll(int $limit = 100): array
    {
        return $this->db->fetchAll(
            "SELECT h.*, u.nombre as usuario_nombre, p.nombre as producto_nombre 
             FROM historico_productos h
             LEFT JOIN usuarios u ON h.usuario_id = u.id
             LEFT JOIN productos p ON h.producto_id = p.id
             ORDER BY h.created_at DESC
             LIMIT ?",
            [$limit]
        );
    }
}

