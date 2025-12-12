<?php

namespace OrionERP\Services;

use OrionERP\Core\Database;

class GraficoService
{
    private $db;

    public function __construct()
    {
        $this->db = Database::getInstance();
    }

    public function getDatosGraficoVentas(string $fechaInicio, string $fechaFin, string $agrupacion = 'dia'): array
    {
        $formato = match($agrupacion) {
            'dia' => '%Y-%m-%d',
            'semana' => '%Y-%u',
            'mes' => '%Y-%m',
            default => '%Y-%m-%d'
        };

        return $this->db->fetchAll(
            "SELECT 
                DATE_FORMAT(fecha, ?) as periodo,
                SUM(total) as total,
                COUNT(*) as cantidad
             FROM pedidos_venta
             WHERE fecha BETWEEN ? AND ? AND estado != 'cancelado'
             GROUP BY DATE_FORMAT(fecha, ?)
             ORDER BY periodo ASC",
            [$formato, $fechaInicio, $fechaFin, $formato]
        );
    }

    public function getDatosGraficoProductos(int $limit = 10): array
    {
        return $this->db->fetchAll(
            "SELECT 
                p.nombre,
                SUM(lpv.cantidad) as cantidad_vendida,
                SUM(lpv.subtotal) as total_ventas
             FROM productos p
             INNER JOIN lineas_pedido_venta lpv ON p.id = lpv.producto_id
             INNER JOIN pedidos_venta pv ON lpv.pedido_id = pv.id
             WHERE pv.estado != 'cancelado'
             GROUP BY p.id, p.nombre
             ORDER BY cantidad_vendida DESC
             LIMIT ?",
            [$limit]
        );
    }

    public function getDatosGraficoCategorias(string $fechaInicio = null, string $fechaFin = null): array
    {
        $where = "pv.estado != 'cancelado'";
        $params = [];

        if ($fechaInicio && $fechaFin) {
            $where .= " AND pv.fecha BETWEEN ? AND ?";
            $params = [$fechaInicio, $fechaFin];
        }

        return $this->db->fetchAll(
            "SELECT 
                c.nombre as categoria,
                SUM(lpv.cantidad) as cantidad_vendida,
                SUM(lpv.subtotal) as total_ventas
             FROM categorias c
             INNER JOIN productos p ON c.id = p.categoria_id
             INNER JOIN lineas_pedido_venta lpv ON p.id = lpv.producto_id
             INNER JOIN pedidos_venta pv ON lpv.pedido_id = pv.id
             WHERE $where
             GROUP BY c.id, c.nombre
             ORDER BY total_ventas DESC",
            $params
        );
    }
}

