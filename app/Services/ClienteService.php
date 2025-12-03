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
}
