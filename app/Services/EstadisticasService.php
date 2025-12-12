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

    public function getVentasPorDia(string $fechaInicio, string $fechaFin): array
    {
        return $this->db->fetchAll(
            "SELECT DATE(fecha) as dia, SUM(total) as total, COUNT(*) as cantidad
             FROM pedidos_venta
             WHERE fecha BETWEEN ? AND ? AND estado != 'cancelado'
             GROUP BY DATE(fecha)
             ORDER BY dia",
            [$fechaInicio, $fechaFin]
        );
    }

    public function getVentasPorCliente(int $limit = 10, string $fechaInicio = null, string $fechaFin = null): array
    {
        $where = "pv.estado != 'cancelado'";
        $params = [];

        if ($fechaInicio && $fechaFin) {
            $where .= " AND pv.fecha BETWEEN ? AND ?";
            $params = [$fechaInicio, $fechaFin];
        }

        $params[] = $limit;

        return $this->db->fetchAll(
            "SELECT c.nombre, SUM(pv.total) as total_ventas, COUNT(pv.id) as cantidad_pedidos
             FROM clientes c
             INNER JOIN pedidos_venta pv ON c.id = pv.cliente_id
             WHERE $where
             GROUP BY c.id, c.nombre
             ORDER BY total_ventas DESC
             LIMIT ?",
            $params
        );
    }

    public function getComparacionPeriodos(string $periodoActualInicio, string $periodoActualFin, 
                                          string $periodoAnteriorInicio, string $periodoAnteriorFin): array
    {
        $actual = $this->db->fetchOne(
            "SELECT SUM(total) as total, COUNT(*) as cantidad
             FROM pedidos_venta
             WHERE fecha BETWEEN ? AND ? AND estado != 'cancelado'",
            [$periodoActualInicio, $periodoActualFin]
        );

        $anterior = $this->db->fetchOne(
            "SELECT SUM(total) as total, COUNT(*) as cantidad
             FROM pedidos_venta
             WHERE fecha BETWEEN ? AND ? AND estado != 'cancelado'",
            [$periodoAnteriorInicio, $periodoAnteriorFin]
        );

        $totalActual = (float) ($actual['total'] ?? 0);
        $totalAnterior = (float) ($anterior['total'] ?? 0);
        $variacion = $totalAnterior > 0 
            ? (($totalActual - $totalAnterior) / $totalAnterior) * 100 
            : 0;

        return [
            'periodo_actual' => [
                'total' => $totalActual,
                'cantidad' => (int) ($actual['cantidad'] ?? 0)
            ],
            'periodo_anterior' => [
                'total' => $totalAnterior,
                'cantidad' => (int) ($anterior['cantidad'] ?? 0)
            ],
            'variacion_porcentual' => round($variacion, 2)
        ];
    }

    public function getVentasPorVendedor(string $fechaInicio = null, string $fechaFin = null): array
    {
        $where = "pv.estado != 'cancelado'";
        $params = [];

        if ($fechaInicio && $fechaFin) {
            $where .= " AND pv.fecha BETWEEN ? AND ?";
            $params = [$fechaInicio, $fechaFin];
        }

        return $this->db->fetchAll(
            "SELECT 
                u.id,
                u.nombre as vendedor,
                COUNT(pv.id) as total_pedidos,
                SUM(pv.total) as total_ventas,
                AVG(pv.total) as promedio_venta
             FROM usuarios u
             INNER JOIN pedidos_venta pv ON u.id = pv.usuario_id
             WHERE $where
             GROUP BY u.id, u.nombre
             ORDER BY total_ventas DESC",
            $params
        );
    }

    public function getTendenciasVentas(int $meses = 6): array
    {
        return $this->db->fetchAll(
            "SELECT 
                DATE_FORMAT(fecha, '%Y-%m') as periodo,
                SUM(total) as total_ventas,
                COUNT(*) as cantidad_pedidos,
                AVG(total) as ticket_promedio
             FROM pedidos_venta
             WHERE fecha >= DATE_SUB(CURDATE(), INTERVAL ? MONTH)
             AND estado != 'cancelado'
             GROUP BY DATE_FORMAT(fecha, '%Y-%m')
             ORDER BY periodo ASC",
            [$meses]
        );
    }
}

