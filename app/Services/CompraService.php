<?php

namespace OrionERP\Services;

use OrionERP\Models\PedidoCompra;
use OrionERP\Models\Proveedor;
use OrionERP\Core\Database;

class CompraService
{
    private $pedidoCompraModel;
    private $proveedorModel;
    private $db;

    public function __construct()
    {
        $this->pedidoCompraModel = new PedidoCompra();
        $this->proveedorModel = new Proveedor();
        $this->db = Database::getInstance();
    }

    public function getResumenCompras(string $fechaInicio, string $fechaFin): array
    {
        $result = $this->db->fetchOne(
            "SELECT 
                COUNT(*) as total_pedidos,
                SUM(total) as total_compras,
                AVG(total) as promedio_compra
             FROM pedidos_compra
             WHERE fecha BETWEEN ? AND ? AND estado != 'cancelado'",
            [$fechaInicio, $fechaFin]
        );
        
        return $result ?? [];
    }

    public function getComprasPorProveedor(string $fechaInicio, string $fechaFin): array
    {
        return $this->db->fetchAll(
            "SELECT 
                p.nombre as proveedor,
                COUNT(pc.id) as total_pedidos,
                SUM(pc.total) as total_comprado
             FROM pedidos_compra pc
             INNER JOIN proveedores p ON pc.proveedor_id = p.id
             WHERE pc.fecha BETWEEN ? AND ? AND pc.estado != 'cancelado'
             GROUP BY p.id, p.nombre
             ORDER BY total_comprado DESC",
            [$fechaInicio, $fechaFin]
        );
    }

    public function getPedidosAtrasados(): array
    {
        return $this->db->fetchAll(
            "SELECT pc.*, p.nombre as proveedor_nombre
             FROM pedidos_compra pc
             LEFT JOIN proveedores p ON pc.proveedor_id = p.id
             WHERE pc.estado IN ('pendiente', 'parcial')
             AND pc.fecha_entrega_esperada < CURDATE()
             ORDER BY pc.fecha_entrega_esperada ASC"
        );
    }
}

