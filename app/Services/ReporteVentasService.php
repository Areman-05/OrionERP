<?php

namespace OrionERP\Services;

use OrionERP\Core\Database;

class ReporteVentasService
{
    private $db;

    public function __construct()
    {
        $this->db = Database::getInstance();
    }

    public function generarReporteMensual(int $mes, int $ano): array
    {
        $ventas = $this->db->fetchAll(
            "SELECT DATE(fecha) as dia, COUNT(*) as cantidad, SUM(total) as total
             FROM pedidos_venta
             WHERE MONTH(fecha) = ? AND YEAR(fecha) = ? AND estado != 'cancelado'
             GROUP BY DATE(fecha)
             ORDER BY dia ASC",
            [$mes, $ano]
        );

        $totalMes = $this->db->fetchOne(
            "SELECT COUNT(*) as cantidad, SUM(total) as total
             FROM pedidos_venta
             WHERE MONTH(fecha) = ? AND YEAR(fecha) = ? AND estado != 'cancelado'",
            [$mes, $ano]
        );

        return [
            'ventas_diarias' => $ventas,
            'resumen' => [
                'total_pedidos' => (int) ($totalMes['cantidad'] ?? 0),
                'total_ventas' => (float) ($totalMes['total'] ?? 0)
            ]
        ];
    }

    public function generarReporteAnual(int $ano): array
    {
        $ventas = $this->db->fetchAll(
            "SELECT MONTH(fecha) as mes, COUNT(*) as cantidad, SUM(total) as total
             FROM pedidos_venta
             WHERE YEAR(fecha) = ? AND estado != 'cancelado'
             GROUP BY MONTH(fecha)
             ORDER BY mes ASC",
            [$ano]
        );

        return $ventas;
    }
}


