<?php

namespace OrionERP\Services;

use OrionERP\Core\Database;

class EstadisticasService
{
    private $db;

    public function __construct()
    {
        $this->db = Database::getInstance();
    }

    public function getVentasPorMes(int $year): array
    {
        return $this->db->fetchAll(
            "SELECT MONTH(fecha) as mes, SUM(total) as total, COUNT(*) as cantidad
             FROM pedidos_venta
             WHERE YEAR(fecha) = ? AND estado != 'cancelado'
             GROUP BY MONTH(fecha)
             ORDER BY mes",
            [$year]
        );
    }

    public function getProductosMasVendidos(int $limit = 10): array
    {
        return $this->db->fetchAll(
            "SELECT p.nombre, SUM(lpv.cantidad) as cantidad_vendida, SUM(lpv.total) as total_ventas
             FROM lineas_pedido_venta lpv
             LEFT JOIN productos p ON lpv.producto_id = p.id
             LEFT JOIN pedidos_venta pv ON lpv.pedido_id = pv.id
             WHERE pv.estado != 'cancelado'
             GROUP BY p.id, p.nombre
             ORDER BY cantidad_vendida DESC
             LIMIT ?",
            [$limit]
        );
    }

    public function getBeneficios(int $year): array
    {
        return $this->db->fetchAll(
            "SELECT 
                MONTH(pv.fecha) as mes,
                SUM(pv.total) as ventas,
                SUM(pc.total) as compras,
                (SUM(pv.total) - SUM(pc.total)) as beneficio
             FROM pedidos_venta pv
             LEFT JOIN pedidos_compra pc ON MONTH(pc.fecha) = MONTH(pv.fecha) AND YEAR(pc.fecha) = YEAR(pv.fecha)
             WHERE YEAR(pv.fecha) = ? AND pv.estado != 'cancelado'
             GROUP BY MONTH(pv.fecha)
             ORDER BY mes",
            [$year]
        );
    }

    public function getKPIs(): array
    {
        $ventasMes = $this->db->fetchOne(
            "SELECT SUM(total) as total, COUNT(*) as cantidad
             FROM pedidos_venta
             WHERE MONTH(fecha) = MONTH(CURRENT_DATE()) 
             AND YEAR(fecha) = YEAR(CURRENT_DATE())
             AND estado != 'cancelado'"
        );
        
        $productosStockBajo = $this->db->fetchOne(
            "SELECT COUNT(*) as cantidad
             FROM productos
             WHERE stock_actual <= stock_minimo AND activo = 1"
        );
        
        $clientesActivos = $this->db->fetchOne(
            "SELECT COUNT(*) as cantidad
             FROM clientes
             WHERE estado = 'activo'"
        );
        
        $pedidosPendientes = $this->db->fetchOne(
            "SELECT COUNT(*) as cantidad
             FROM pedidos_venta
             WHERE estado = 'pendiente'"
        );
        
        return [
            'ventas_mes' => $ventasMes['total'] ?? 0,
            'cantidad_ventas_mes' => $ventasMes['cantidad'] ?? 0,
            'productos_stock_bajo' => $productosStockBajo['cantidad'] ?? 0,
            'clientes_activos' => $clientesActivos['cantidad'] ?? 0,
            'pedidos_pendientes' => $pedidosPendientes['cantidad'] ?? 0
        ];
    }
}

