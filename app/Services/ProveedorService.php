<?php

namespace OrionERP\Services;

use OrionERP\Models\Proveedor;
use OrionERP\Models\PedidoCompra;
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

    public function getProveedorCompleto(int $proveedorId): ?array
    {
        $proveedor = $this->proveedorModel->findById($proveedorId);
        
        if (!$proveedor) {
            return null;
        }
        
        $proveedor['total_pedidos'] = $this->getTotalPedidos($proveedorId);
        $proveedor['total_comprado'] = $this->getTotalComprado($proveedorId);
        $proveedor['pedidos_pendientes'] = $this->getPedidosPendientes($proveedorId);
        
        return $proveedor;
    }

    public function getTotalPedidos(int $proveedorId): int
    {
        $result = $this->db->fetchOne(
            "SELECT COUNT(*) as total FROM pedidos_compra WHERE proveedor_id = ? AND estado != 'cancelado'",
            [$proveedorId]
        );
        
        return (int) ($result['total'] ?? 0);
    }

    public function getTotalComprado(int $proveedorId): float
    {
        $result = $this->db->fetchOne(
            "SELECT SUM(total) as total FROM pedidos_compra WHERE proveedor_id = ? AND estado != 'cancelado'",
            [$proveedorId]
        );
        
        return (float) ($result['total'] ?? 0);
    }

    public function getPedidosPendientes(int $proveedorId): int
    {
        $result = $this->db->fetchOne(
            "SELECT COUNT(*) as total FROM pedidos_compra 
             WHERE proveedor_id = ? AND estado IN ('pendiente', 'parcial')",
            [$proveedorId]
        );
        
        return (int) ($result['total'] ?? 0);
    }
}


