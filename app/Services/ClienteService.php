<?php

namespace OrionERP\Services;

use OrionERP\Models\Cliente;
use OrionERP\Core\Database;

class ClienteService
{
    private $clienteModel;
    private $db;

    public function __construct()
    {
        $this->clienteModel = new Cliente();
        $this->db = Database::getInstance();
    }

    public function getEstadisticasCliente(int $clienteId): array
    {
        $pedidos = $this->db->fetchOne(
            "SELECT COUNT(*) as total FROM pedidos_venta WHERE cliente_id = ?",
            [$clienteId]
        );

        $totalVentas = $this->db->fetchOne(
            "SELECT SUM(total) as total FROM pedidos_venta 
             WHERE cliente_id = ? AND estado != 'cancelado'",
            [$clienteId]
        );

        $facturasPendientes = $this->db->fetchOne(
            "SELECT COUNT(*) as total FROM facturas 
             WHERE cliente_id = ? AND estado = 'pendiente'",
            [$clienteId]
        );

        return [
            'total_pedidos' => (int) ($pedidos['total'] ?? 0),
            'total_ventas' => (float) ($totalVentas['total'] ?? 0),
            'facturas_pendientes' => (int) ($facturasPendientes['total'] ?? 0)
        ];
    }

    public function getClientesActivos(): array
    {
        return $this->db->fetchAll(
            "SELECT * FROM clientes WHERE estado = 'activo' ORDER BY nombre ASC"
        );
    }

    public function getClientesMorosos(): array
    {
        return $this->db->fetchAll(
            "SELECT c.*, SUM(f.total) as total_pendiente 
             FROM clientes c
             INNER JOIN facturas f ON c.id = f.cliente_id
             WHERE f.estado = 'pendiente' AND f.fecha_vencimiento < CURDATE()
             GROUP BY c.id
             ORDER BY total_pendiente DESC"
        );
    }

    public function getHistorialCompras(int $clienteId, int $limit = 10): array
    {
        return $this->db->fetchAll(
            "SELECT 
                pv.id,
                pv.numero_pedido,
                pv.fecha,
                pv.total,
                pv.estado,
                COUNT(lpv.id) as total_items
             FROM pedidos_venta pv
             LEFT JOIN lineas_pedido_venta lpv ON pv.id = lpv.pedido_id
             WHERE pv.cliente_id = ?
             GROUP BY pv.id, pv.numero_pedido, pv.fecha, pv.total, pv.estado
             ORDER BY pv.fecha DESC
             LIMIT ?",
            [$clienteId, $limit]
        );
    }

    public function buscarClientes(string $termino): array
    {
        $termino = "%$termino%";
        return $this->db->fetchAll(
            "SELECT * FROM clientes 
             WHERE (nombre LIKE ? OR codigo LIKE ? OR email LIKE ? OR telefono LIKE ?)
             ORDER BY nombre ASC
             LIMIT 50",
            [$termino, $termino, $termino, $termino]
        );
    }

    public function getClientesPorSegmento(): array
    {
        return $this->db->fetchAll(
            "SELECT 
                CASE 
                    WHEN total_compras >= 10000 THEN 'VIP'
                    WHEN total_compras >= 5000 THEN 'Premium'
                    WHEN total_compras >= 1000 THEN 'Regular'
                    ELSE 'Nuevo'
                END as segmento,
                COUNT(*) as cantidad,
                SUM(total_compras) as total_ventas
             FROM (
                 SELECT 
                     c.id,
                     COALESCE(SUM(pv.total), 0) as total_compras
                 FROM clientes c
                 LEFT JOIN pedidos_venta pv ON c.id = pv.cliente_id AND pv.estado != 'cancelado'
                 GROUP BY c.id
             ) as segmentos
             GROUP BY segmento
             ORDER BY total_ventas DESC"
        );
    }
}
