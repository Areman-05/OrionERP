<?php

namespace OrionERP\Models;

use OrionERP\Core\Database;

class SeguimientoEntrega
{
    private $db;

    public function __construct()
    {
        $this->db = Database::getInstance();
    }

    public function getByPedido(int $pedidoId): array
    {
        return $this->db->fetchAll(
            "SELECT s.*, u.nombre as usuario_nombre 
             FROM seguimiento_entregas s
             LEFT JOIN usuarios u ON s.usuario_id = u.id
             WHERE s.pedido_compra_id = ?
             ORDER BY s.fecha DESC",
            [$pedidoId]
        );
    }

    public function create(array $data): int
    {
        $sql = "INSERT INTO seguimiento_entregas (pedido_compra_id, estado, fecha, ubicacion, notas, usuario_id) 
                VALUES (?, ?, ?, ?, ?, ?)";
        
        $this->db->query($sql, [
            $data['pedido_compra_id'],
            $data['estado'],
            $data['fecha'] ?? date('Y-m-d H:i:s'),
            $data['ubicacion'] ?? null,
            $data['notas'] ?? null,
            $data['usuario_id'] ?? null
        ]);

        return (int) $this->db->lastInsertId();
    }
}

