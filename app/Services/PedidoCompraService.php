<?php

namespace OrionERP\Services;

use OrionERP\Core\Database;

class PedidoCompraService
{
    private $db;

    public function __construct()
    {
        $this->db = Database::getInstance();
    }

    public function getPedidosPorEstado(string $estado): array
    {
        return $this->db->fetchAll(
            "SELECT p.*, pr.nombre as proveedor_nombre 
             FROM pedidos_compra p
             LEFT JOIN proveedores pr ON p.proveedor_id = pr.id
             WHERE p.estado = ?
             ORDER BY p.fecha DESC",
            [$estado]
        );
    }

    public function getPedidosPendientes(): array
    {
        return $this->db->fetchAll(
            "SELECT p.*, pr.nombre as proveedor_nombre 
             FROM pedidos_compra p
             LEFT JOIN proveedores pr ON p.proveedor_id = pr.id
             WHERE p.estado IN ('pendiente', 'parcialmente_recibido')
             ORDER BY p.fecha_esperada ASC"
        );
    }

    public function getResumenPedidos(string $fechaInicio, string $fechaFin): array
    {
        $total = $this->db->fetchOne(
            "SELECT COUNT(*) as total, SUM(total) as valor_total 
             FROM pedidos_compra 
             WHERE fecha BETWEEN ? AND ? AND estado != 'cancelado'",
            [$fechaInicio, $fechaFin]
        );

        return [
            'total_pedidos' => (int) ($total['total'] ?? 0),
            'valor_total' => (float) ($total['valor_total'] ?? 0)
        ];
    }
}

