<?php

namespace OrionERP\Services;

use OrionERP\Core\Database;

class ReporteComprasService
{
    private $db;

    public function __construct()
    {
        $this->db = Database::getInstance();
    }

    public function generarReporteMensual(int $mes, int $ano): array
    {
        $compras = $this->db->fetchAll(
            "SELECT DATE(fecha) as dia, COUNT(*) as cantidad, SUM(total) as total
             FROM pedidos_compra
             WHERE MONTH(fecha) = ? AND YEAR(fecha) = ? AND estado != 'cancelado'
             GROUP BY DATE(fecha)
             ORDER BY dia ASC",
            [$mes, $ano]
        );

        $totalMes = $this->db->fetchOne(
            "SELECT COUNT(*) as cantidad, SUM(total) as total
             FROM pedidos_compra
             WHERE MONTH(fecha) = ? AND YEAR(fecha) = ? AND estado != 'cancelado'",
            [$mes, $ano]
        );

        return [
            'compras_diarias' => $compras,
            'resumen' => [
                'total_pedidos' => (int) ($totalMes['cantidad'] ?? 0),
                'total_compras' => (float) ($totalMes['total'] ?? 0)
            ]
        ];
    }

    public function generarReportePorProveedor(string $fechaInicio, string $fechaFin): array
    {
        return $this->db->fetchAll(
            "SELECT p.nombre as proveedor, COUNT(pc.id) as pedidos, SUM(pc.total) as total
             FROM pedidos_compra pc
             LEFT JOIN proveedores p ON pc.proveedor_id = p.id
             WHERE pc.fecha BETWEEN ? AND ? AND pc.estado != 'cancelado'
             GROUP BY p.id, p.nombre
             ORDER BY total DESC",
            [$fechaInicio, $fechaFin]
        );
    }
}

