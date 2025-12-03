<?php

namespace OrionERP\Services;

use OrionERP\Core\Database;

class PedidoService
{
    private $db;

    public function __construct()
    {
        $this->db = Database::getInstance();
    }

    public function getPedidosPorEstado(string $estado): array
    {
        return $this->db->fetchAll(
            "SELECT p.*, c.nombre as cliente_nombre 
             FROM pedidos_venta p
             LEFT JOIN clientes c ON p.cliente_id = c.id
             WHERE p.estado = ?
             ORDER BY p.fecha DESC",
            [$estado]
        );
    }

    public function getPedidosPendientes(): array
    {
        return $this->getPedidosPorEstado('pendiente');
    }

    public function getResumenPedidos(string $fechaInicio, string $fechaFin): array
    {
        $total = $this->db->fetchOne(
            "SELECT COUNT(*) as total, SUM(total) as valor_total 
             FROM pedidos_venta 
             WHERE fecha BETWEEN ? AND ? AND estado != 'cancelado'",
            [$fechaInicio, $fechaFin]
        );

        $porEstado = $this->db->fetchAll(
            "SELECT estado, COUNT(*) as cantidad, SUM(total) as valor
             FROM pedidos_venta
             WHERE fecha BETWEEN ? AND ?
             GROUP BY estado",
            [$fechaInicio, $fechaFin]
        );

        return [
            'total_pedidos' => (int) ($total['total'] ?? 0),
            'valor_total' => (float) ($total['valor_total'] ?? 0),
            'por_estado' => $porEstado
        ];
    }
}

