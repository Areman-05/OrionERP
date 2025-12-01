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

    public function getResumenGeneral(): array
    {
        return [
            'ventas_hoy' => $this->getVentasHoy(),
            'ventas_mes' => $this->getVentasMes(),
            'productos_stock_bajo' => $this->getProductosStockBajo(),
            'pedidos_pendientes' => $this->getPedidosPendientes(),
            'facturas_pendientes' => $this->getFacturasPendientes(),
            'clientes_nuevos_mes' => $this->getClientesNuevosMes()
        ];
    }

    private function getVentasHoy(): float
    {
        $result = $this->db->fetchOne(
            "SELECT SUM(total) as total FROM pedidos_venta 
             WHERE DATE(fecha) = CURDATE() AND estado != 'cancelado'"
        );
        
        return (float) ($result['total'] ?? 0);
    }

    private function getVentasMes(): float
    {
        $result = $this->db->fetchOne(
            "SELECT SUM(total) as total FROM pedidos_venta 
             WHERE MONTH(fecha) = MONTH(CURRENT_DATE()) 
             AND YEAR(fecha) = YEAR(CURRENT_DATE()) 
             AND estado != 'cancelado'"
        );
        
        return (float) ($result['total'] ?? 0);
    }

    private function getProductosStockBajo(): int
    {
        $result = $this->db->fetchOne(
            "SELECT COUNT(*) as total FROM productos 
             WHERE stock_actual <= stock_minimo AND activo = 1"
        );
        
        return (int) ($result['total'] ?? 0);
    }

    private function getPedidosPendientes(): int
    {
        $result = $this->db->fetchOne(
            "SELECT COUNT(*) as total FROM pedidos_venta WHERE estado = 'pendiente'"
        );
        
        return (int) ($result['total'] ?? 0);
    }

    private function getFacturasPendientes(): int
    {
        $result = $this->db->fetchOne(
            "SELECT COUNT(*) as total FROM facturas WHERE estado = 'pendiente'"
        );
        
        return (int) ($result['total'] ?? 0);
    }

    private function getClientesNuevosMes(): int
    {
        $result = $this->db->fetchOne(
            "SELECT COUNT(*) as total FROM clientes 
             WHERE MONTH(created_at) = MONTH(CURRENT_DATE()) 
             AND YEAR(created_at) = YEAR(CURRENT_DATE())"
        );
        
        return (int) ($result['total'] ?? 0);
    }

    public function getVentasUltimosMeses(int $meses = 6): array
    {
        return $this->db->fetchAll(
            "SELECT 
                DATE_FORMAT(fecha, '%Y-%m') as mes,
                SUM(total) as total,
                COUNT(*) as cantidad
             FROM pedidos_venta
             WHERE fecha >= DATE_SUB(CURRENT_DATE(), INTERVAL ? MONTH)
             AND estado != 'cancelado'
             GROUP BY DATE_FORMAT(fecha, '%Y-%m')
             ORDER BY mes ASC",
            [$meses]
        );
    }
}

