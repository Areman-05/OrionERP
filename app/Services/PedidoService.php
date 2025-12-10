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

    public function getPedidosPorCliente(int $clienteId, int $limit = 10): array
    {
        return $this->db->fetchAll(
            "SELECT p.*, 
                    (SELECT COUNT(*) FROM lineas_pedido_venta WHERE pedido_id = p.id) as total_lineas
             FROM pedidos_venta p
             WHERE p.cliente_id = ? AND p.estado != 'cancelado'
             ORDER BY p.fecha DESC
             LIMIT ?",
            [$clienteId, $limit]
        );
    }

    public function getPedidosPorVendedor(int $usuarioId, string $fechaInicio = null, string $fechaFin = null): array
    {
        $where = "p.usuario_id = ? AND p.estado != 'cancelado'";
        $params = [$usuarioId];

        if ($fechaInicio && $fechaFin) {
            $where .= " AND p.fecha BETWEEN ? AND ?";
            $params[] = $fechaInicio;
            $params[] = $fechaFin;
        }

        return $this->db->fetchAll(
            "SELECT p.*, c.nombre as cliente_nombre, SUM(lpv.cantidad) as total_items
             FROM pedidos_venta p
             LEFT JOIN clientes c ON p.cliente_id = c.id
             LEFT JOIN lineas_pedido_venta lpv ON p.id = lpv.pedido_id
             WHERE $where
             GROUP BY p.id
             ORDER BY p.fecha DESC",
            $params
        );
    }

    public function calcularEstadisticasPedidos(string $fechaInicio, string $fechaFin): array
    {
        $estadisticas = $this->db->fetchOne(
            "SELECT 
                COUNT(*) as total_pedidos,
                SUM(total) as valor_total,
                AVG(total) as ticket_promedio,
                MIN(total) as ticket_minimo,
                MAX(total) as ticket_maximo,
                COUNT(DISTINCT cliente_id) as clientes_unicos
             FROM pedidos_venta
             WHERE fecha BETWEEN ? AND ? AND estado != 'cancelado'",
            [$fechaInicio, $fechaFin]
        );

        return [
            'total_pedidos' => (int) ($estadisticas['total_pedidos'] ?? 0),
            'valor_total' => (float) ($estadisticas['valor_total'] ?? 0),
            'ticket_promedio' => (float) ($estadisticas['ticket_promedio'] ?? 0),
            'ticket_minimo' => (float) ($estadisticas['ticket_minimo'] ?? 0),
            'ticket_maximo' => (float) ($estadisticas['ticket_maximo'] ?? 0),
            'clientes_unicos' => (int) ($estadisticas['clientes_unicos'] ?? 0)
        ];
    }
}

