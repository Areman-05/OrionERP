<?php

namespace OrionERP\Services;

use OrionERP\Models\Proveedor;
use OrionERP\Core\Database;

class ProveedorService
{
    private $proveedorModel;
    private $db;

    public function __construct()
    {
        $this->proveedorModel = new Proveedor();
        $this->db = Database::getInstance();
    }

    public function getEstadisticasProveedor(int $proveedorId): array
    {
        $pedidos = $this->db->fetchOne(
            "SELECT COUNT(*) as total FROM pedidos_compra WHERE proveedor_id = ?",
            [$proveedorId]
        );

        $totalCompras = $this->db->fetchOne(
            "SELECT SUM(total) as total FROM pedidos_compra 
             WHERE proveedor_id = ? AND estado != 'cancelado'",
            [$proveedorId]
        );

        $pedidosPendientes = $this->db->fetchOne(
            "SELECT COUNT(*) as total FROM pedidos_compra 
             WHERE proveedor_id = ? AND estado IN ('pendiente', 'parcialmente_recibido')",
            [$proveedorId]
        );

        return [
            'total_pedidos' => (int) ($pedidos['total'] ?? 0),
            'total_compras' => (float) ($totalCompras['total'] ?? 0),
            'pedidos_pendientes' => (int) ($pedidosPendientes['total'] ?? 0)
        ];
    }

    public function getProveedoresActivos(): array
    {
        return $this->db->fetchAll(
            "SELECT * FROM proveedores WHERE activo = 1 ORDER BY nombre ASC"
        );
    }

    public function getProveedoresConPedidosPendientes(): array
    {
        return $this->db->fetchAll(
            "SELECT DISTINCT p.*, COUNT(pc.id) as pedidos_pendientes
             FROM proveedores p
             INNER JOIN pedidos_compra pc ON p.id = pc.proveedor_id
             WHERE pc.estado IN ('pendiente', 'parcialmente_recibido')
             GROUP BY p.id
             ORDER BY pedidos_pendientes DESC"
        );
    }
}
