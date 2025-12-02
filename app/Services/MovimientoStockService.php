<?php

namespace OrionERP\Services;

use OrionERP\Core\Database;

class MovimientoStockService
{
    private $db;

    public function __construct()
    {
        $this->db = Database::getInstance();
    }

    public function getMovimientosPorPeriodo(string $fechaInicio, string $fechaFin): array
    {
        return $this->db->fetchAll(
            "SELECT ms.*, p.nombre as producto_nombre, p.codigo as producto_codigo, u.nombre as usuario_nombre
             FROM movimientos_stock ms
             LEFT JOIN productos p ON ms.producto_id = p.id
             LEFT JOIN usuarios u ON ms.usuario_id = u.id
             WHERE DATE(ms.created_at) BETWEEN ? AND ?
             ORDER BY ms.created_at DESC",
            [$fechaInicio, $fechaFin]
        );
    }

    public function getMovimientosPorTipo(string $tipo, int $limit = 100): array
    {
        return $this->db->fetchAll(
            "SELECT ms.*, p.nombre as producto_nombre, u.nombre as usuario_nombre
             FROM movimientos_stock ms
             LEFT JOIN productos p ON ms.producto_id = p.id
             LEFT JOIN usuarios u ON ms.usuario_id = u.id
             WHERE ms.tipo = ?
             ORDER BY ms.created_at DESC
             LIMIT ?",
            [$tipo, $limit]
        );
    }

    public function getResumenMovimientos(string $fechaInicio, string $fechaFin): array
    {
        $entradas = $this->db->fetchOne(
            "SELECT SUM(cantidad) as total FROM movimientos_stock 
             WHERE tipo = 'entrada' AND DATE(created_at) BETWEEN ? AND ?",
            [$fechaInicio, $fechaFin]
        );
        
        $salidas = $this->db->fetchOne(
            "SELECT SUM(cantidad) as total FROM movimientos_stock 
             WHERE tipo = 'salida' AND DATE(created_at) BETWEEN ? AND ?",
            [$fechaInicio, $fechaFin]
        );
        
        return [
            'entradas' => (int) ($entradas['total'] ?? 0),
            'salidas' => (int) ($salidas['total'] ?? 0),
            'diferencia' => (int) (($entradas['total'] ?? 0) - ($salidas['total'] ?? 0))
        ];
    }
}

