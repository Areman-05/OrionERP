<?php

namespace OrionERP\Services;

use OrionERP\Core\Database;

class DashboardService
{
    private $db;

    public function __construct()
    {
        $this->db = Database::getInstance();
    }

    public function getKPIs(): array
    {
        $ventasMes = $this->db->fetchOne(
            "SELECT SUM(total) as total FROM pedidos_venta 
             WHERE MONTH(fecha) = MONTH(CURDATE()) AND YEAR(fecha) = YEAR(CURDATE()) 
             AND estado != 'cancelado'"
        );

        $comprasMes = $this->db->fetchOne(
            "SELECT SUM(total) as total FROM pedidos_compra 
             WHERE MONTH(fecha) = MONTH(CURDATE()) AND YEAR(fecha) = YEAR(CURDATE()) 
             AND estado != 'cancelado'"
        );

        $pedidosPendientes = $this->db->fetchOne(
            "SELECT COUNT(*) as total FROM pedidos_venta WHERE estado = 'pendiente'"
        );

        $productosStockBajo = $this->db->fetchOne(
            "SELECT COUNT(*) as total FROM productos 
             WHERE stock_actual <= stock_minimo AND activo = 1"
        );

        return [
            'ventas_mes' => (float) ($ventasMes['total'] ?? 0),
            'compras_mes' => (float) ($comprasMes['total'] ?? 0),
            'beneficio_mes' => (float) (($ventasMes['total'] ?? 0) - ($comprasMes['total'] ?? 0)),
            'pedidos_pendientes' => (int) ($pedidosPendientes['total'] ?? 0),
            'productos_stock_bajo' => (int) ($productosStockBajo['total'] ?? 0)
        ];
    }

    public function getVentasUltimosMeses(int $meses = 6): array
    {
        return $this->db->fetchAll(
            "SELECT DATE_FORMAT(fecha, '%Y-%m') as mes, SUM(total) as total
             FROM pedidos_venta
             WHERE fecha >= DATE_SUB(CURDATE(), INTERVAL ? MONTH) AND estado != 'cancelado'
             GROUP BY DATE_FORMAT(fecha, '%Y-%m')
             ORDER BY mes ASC",
            [$meses]
        );
    }
}
