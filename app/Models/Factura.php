<?php

namespace OrionERP\Models;

use OrionERP\Core\Database;

class Factura
{
    private $db;

    public function __construct()
    {
        $this->db = Database::getInstance();
    }

    public function getAll(): array
    {
        return $this->db->fetchAll(
            "SELECT f.*, c.nombre as cliente_nombre 
             FROM facturas f
             LEFT JOIN clientes c ON f.cliente_id = c.id
             ORDER BY f.fecha_emision DESC"
        );
    }

    public function findById(int $id): ?array
    {
        return $this->db->fetchOne(
            "SELECT f.*, c.nombre as cliente_nombre, c.direccion as cliente_direccion, c.ciudad as cliente_ciudad
             FROM facturas f
             LEFT JOIN clientes c ON f.cliente_id = c.id
             WHERE f.id = ?",
            [$id]
        );
    }

    public function create(array $data): int
    {
        $numeroFactura = $this->generarNumeroFactura();
        
        $sql = "INSERT INTO facturas (numero_factura, pedido_id, cliente_id, fecha_emision, fecha_vencimiento, estado, subtotal, impuestos, total) 
                VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)";
        
        $this->db->query($sql, [
            $numeroFactura,
            $data['pedido_id'] ?? null,
            $data['cliente_id'],
            $data['fecha_emision'] ?? date('Y-m-d'),
            $data['fecha_vencimiento'] ?? null,
            $data['estado'] ?? 'pendiente',
            $data['subtotal'] ?? 0,
            $data['impuestos'] ?? 0,
            $data['total'] ?? 0
        ]);

        return (int) $this->db->lastInsertId();
    }

    private function generarNumeroFactura(): string
    {
        $year = date('Y');
        $ultimo = $this->db->fetchOne(
            "SELECT numero_factura FROM facturas WHERE numero_factura LIKE ? ORDER BY id DESC LIMIT 1",
            ["FAC-$year-%"]
        );

        if ($ultimo) {
            $numero = (int) substr($ultimo['numero_factura'], -4) + 1;
        } else {
            $numero = 1;
        }

        return sprintf("FAC-%s-%04d", $year, $numero);
    }
}

