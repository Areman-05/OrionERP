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
}

