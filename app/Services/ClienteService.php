<?php

namespace OrionERP\Services;

use OrionERP\Models\Cliente;
use OrionERP\Models\PedidoVenta;
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

    public function getClienteCompleto(int $clienteId): ?array
    {
        $cliente = $this->clienteModel->findById($clienteId);
        
        if (!$cliente) {
            return null;
        }
        
        $cliente['total_pedidos'] = $this->getTotalPedidos($clienteId);
        $cliente['total_comprado'] = $this->getTotalComprado($clienteId);
        $cliente['ultimo_pedido'] = $this->getUltimoPedido($clienteId);
        
        return $cliente;
    }

    public function getTotalPedidos(int $clienteId): int
    {
        $result = $this->db->fetchOne(
            "SELECT COUNT(*) as total FROM pedidos_venta WHERE cliente_id = ? AND estado != 'cancelado'",
            [$clienteId]
        );
        
        return (int) ($result['total'] ?? 0);
    }

    public function getTotalComprado(int $clienteId): float
    {
        $result = $this->db->fetchOne(
            "SELECT SUM(total) as total FROM pedidos_venta WHERE cliente_id = ? AND estado != 'cancelado'",
            [$clienteId]
        );
        
        return (float) ($result['total'] ?? 0);
    }

    public function getUltimoPedido(int $clienteId): ?array
    {
        return $this->db->fetchOne(
            "SELECT * FROM pedidos_venta WHERE cliente_id = ? ORDER BY fecha DESC LIMIT 1",
            [$clienteId]
        );
    }

    public function actualizarEstadoCliente(int $clienteId): void
    {
        $totalComprado = $this->getTotalComprado($clienteId);
        $facturasVencidas = $this->db->fetchOne(
            "SELECT COUNT(*) as total FROM facturas 
             WHERE cliente_id = ? AND estado = 'vencida'",
            [$clienteId]
        );
        
        $nuevoEstado = 'activo';
        
        if ($facturasVencidas['total'] > 0) {
            $nuevoEstado = 'moroso';
        } elseif ($totalComprado > 10000) {
            $nuevoEstado = 'VIP';
        }
        
        $this->clienteModel->update($clienteId, ['estado' => $nuevoEstado]);
    }
}


