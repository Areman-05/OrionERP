<?php

namespace OrionERP\Services;

use OrionERP\Core\Database;

class ReporteService
{
    private $db;

    public function __construct()
    {
        $this->db = Database::getInstance();
    }

    public function getResumenVentas(string $fechaInicio, string $fechaFin): array
    {
        $ventas = $this->db->fetchOne(
            "SELECT 
                COUNT(*) as total_pedidos,
                SUM(total) as total_ventas,
                AVG(total) as promedio_venta,
                SUM(CASE WHEN estado = 'completado' THEN total ELSE 0 END) as ventas_completadas
             FROM pedidos_venta
             WHERE fecha BETWEEN ? AND ? AND estado != 'cancelado'",
            [$fechaInicio, $fechaFin]
        );
        
        return $ventas ?? [];
    }

    public function getTopClientes(int $limit = 10): array
    {
        return $this->db->fetchAll(
            "SELECT 
                c.nombre,
                COUNT(pv.id) as total_pedidos,
                SUM(pv.total) as total_comprado
             FROM clientes c
             INNER JOIN pedidos_venta pv ON c.id = pv.cliente_id
             WHERE pv.estado != 'cancelado'
             GROUP BY c.id, c.nombre
             ORDER BY total_comprado DESC
             LIMIT ?",
            [$limit]
        );
    }

    public function getVentasPorCategoria(string $fechaInicio, string $fechaFin): array
    {
        return $this->db->fetchAll(
            "SELECT 
                c.nombre as categoria,
                COUNT(DISTINCT lpv.producto_id) as productos_vendidos,
                SUM(lpv.cantidad) as cantidad_total,
                SUM(lpv.total) as total_ventas
             FROM lineas_pedido_venta lpv
             INNER JOIN productos p ON lpv.producto_id = p.id
             LEFT JOIN categorias c ON p.categoria_id = c.id
             INNER JOIN pedidos_venta pv ON lpv.pedido_id = pv.id
             WHERE pv.fecha BETWEEN ? AND ? AND pv.estado != 'cancelado'
             GROUP BY c.id, c.nombre
             ORDER BY total_ventas DESC",
            [$fechaInicio, $fechaFin]
        );
    }
}

