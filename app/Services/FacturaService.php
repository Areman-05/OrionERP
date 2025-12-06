<?php

namespace OrionERP\Services;

use OrionERP\Models\Factura;
use OrionERP\Core\Database;

class FacturaService
{
    private $facturaModel;
    private $db;

    public function __construct()
    {
        $this->facturaModel = new Factura();
        $this->db = Database::getInstance();
    }

    public function getFacturasPendientes(): array
    {
        return $this->db->fetchAll(
            "SELECT f.*, c.nombre as cliente_nombre 
             FROM facturas f
             LEFT JOIN clientes c ON f.cliente_id = c.id
             WHERE f.estado = 'pendiente'
             ORDER BY f.fecha_vencimiento ASC"
        );
    }

    public function getFacturasVencidas(): array
    {
        return $this->db->fetchAll(
            "SELECT f.*, c.nombre as cliente_nombre 
             FROM facturas f
             LEFT JOIN clientes c ON f.cliente_id = c.id
             WHERE f.estado = 'pendiente' AND f.fecha_vencimiento < CURDATE()
             ORDER BY f.fecha_vencimiento ASC"
        );
    }

    public function getTotalFacturado(string $fechaInicio, string $fechaFin): float
    {
        $result = $this->db->fetchOne(
            "SELECT SUM(total) as total FROM facturas 
             WHERE fecha_emision BETWEEN ? AND ? AND estado != 'cancelada'",
            [$fechaInicio, $fechaFin]
        );
        
        return (float) ($result['total'] ?? 0);
    }

    public function getTotalPendiente(): float
    {
        $result = $this->db->fetchOne(
            "SELECT SUM(total) as total FROM facturas WHERE estado = 'pendiente'"
        );
        
        return (float) ($result['total'] ?? 0);
    }

    public function marcarVencidas(): int
    {
        $this->db->query(
            "UPDATE facturas SET estado = 'vencida' 
             WHERE estado = 'pendiente' AND fecha_vencimiento < CURDATE()"
        );
        
        $result = $this->db->fetchOne("SELECT ROW_COUNT() as total");
        return (int) ($result['total'] ?? 0);
    }

    public function generarNumeroFactura(): string
    {
        $ano = date('Y');
        $ultimaFactura = $this->db->fetchOne(
            "SELECT numero_factura FROM facturas 
             WHERE numero_factura LIKE ? 
             ORDER BY id DESC LIMIT 1",
            ["FAC-$ano-%"]
        );

        if ($ultimaFactura) {
            $numero = (int) substr($ultimaFactura['numero_factura'], -4);
            $numero++;
        } else {
            $numero = 1;
        }

        return sprintf("FAC-%s-%04d", $ano, $numero);
    }

    public function getFacturasPorCliente(int $clienteId, int $limit = 50): array
    {
        return $this->db->fetchAll(
            "SELECT f.*, 
             (SELECT COUNT(*) FROM lineas_factura WHERE factura_id = f.id) as total_lineas
             FROM facturas f
             WHERE f.cliente_id = ?
             ORDER BY f.fecha_emision DESC
             LIMIT ?",
            [$clienteId, $limit]
        );
    }

    public function getEstadisticasFacturacion(int $mes, int $ano): array
    {
        $total = $this->db->fetchOne(
            "SELECT 
                COUNT(*) as cantidad,
                SUM(total) as total,
                SUM(CASE WHEN estado = 'pagada' THEN total ELSE 0 END) as total_pagado,
                SUM(CASE WHEN estado = 'pendiente' THEN total ELSE 0 END) as total_pendiente
             FROM facturas
             WHERE MONTH(fecha_emision) = ? AND YEAR(fecha_emision) = ?",
            [$mes, $ano]
        );

        return [
            'cantidad' => (int) ($total['cantidad'] ?? 0),
            'total' => (float) ($total['total'] ?? 0),
            'total_pagado' => (float) ($total['total_pagado'] ?? 0),
            'total_pendiente' => (float) ($total['total_pendiente'] ?? 0)
        ];
    }
}
