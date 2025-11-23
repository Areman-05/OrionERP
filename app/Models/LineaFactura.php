<?php

namespace OrionERP\Models;

use OrionERP\Core\Database;

class LineaFactura
{
    private $db;

    public function __construct()
    {
        $this->db = Database::getInstance();
    }

    public function getByFactura(int $facturaId): array
    {
        return $this->db->fetchAll(
            "SELECT lf.*, p.nombre as producto_nombre, p.codigo as producto_codigo
             FROM lineas_factura lf
             LEFT JOIN productos p ON lf.producto_id = p.id
             WHERE lf.factura_id = ?
             ORDER BY lf.orden, lf.id",
            [$facturaId]
        );
    }

    public function create(array $data): int
    {
        $subtotal = ($data['cantidad'] * $data['precio_unitario']) * (1 - ($data['descuento'] ?? 0) / 100);
        $impuesto = $subtotal * (($data['impuesto'] ?? 21) / 100);
        $total = $subtotal + $impuesto;

        $sql = "INSERT INTO lineas_factura (factura_id, producto_id, descripcion, cantidad, precio_unitario, descuento, impuesto, total, orden) 
                VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)";
        
        $this->db->query($sql, [
            $data['factura_id'],
            $data['producto_id'] ?? null,
            $data['descripcion'],
            $data['cantidad'] ?? 1,
            $data['precio_unitario'],
            $data['descuento'] ?? 0,
            $data['impuesto'] ?? 21,
            $total,
            $data['orden'] ?? 0
        ]);

        return (int) $this->db->lastInsertId();
    }

    public function delete(int $id): bool
    {
        $this->db->query("DELETE FROM lineas_factura WHERE id = ?", [$id]);
        return true;
    }
}

