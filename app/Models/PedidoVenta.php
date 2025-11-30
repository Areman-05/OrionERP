<?php

namespace OrionERP\Models;

use OrionERP\Core\Database;

class PedidoVenta
{
    private $db;

    public function __construct()
    {
        $this->db = Database::getInstance();
    }

    public function getAll(): array
    {
        return $this->db->fetchAll(
            "SELECT pv.*, c.nombre as cliente_nombre, u.nombre as usuario_nombre
             FROM pedidos_venta pv
             LEFT JOIN clientes c ON pv.cliente_id = c.id
             LEFT JOIN usuarios u ON pv.usuario_id = u.id
             ORDER BY pv.fecha DESC, pv.created_at DESC"
        );
    }

    public function findById(int $id): ?array
    {
        $pedido = $this->db->fetchOne(
            "SELECT pv.*, c.nombre as cliente_nombre, u.nombre as usuario_nombre
             FROM pedidos_venta pv
             LEFT JOIN clientes c ON pv.cliente_id = c.id
             LEFT JOIN usuarios u ON pv.usuario_id = u.id
             WHERE pv.id = ?",
            [$id]
        );

        if ($pedido) {
            $pedido['lineas'] = $this->getLineas($id);
        }

        return $pedido;
    }

    public function getLineas(int $pedidoId): array
    {
        return $this->db->fetchAll(
            "SELECT lpv.*, pr.nombre as producto_nombre, pr.codigo as producto_codigo
             FROM lineas_pedido_venta lpv
             LEFT JOIN productos pr ON lpv.producto_id = pr.id
             WHERE lpv.pedido_id = ?",
            [$pedidoId]
        );
    }

    public function create(array $data): int
    {
        $numeroPedido = $this->generarNumeroPedido();
        
        $sql = "INSERT INTO pedidos_venta (numero_pedido, cliente_id, fecha, estado, subtotal, descuento, impuestos, total, notas, usuario_id) 
                VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";
        
        $this->db->query($sql, [
            $numeroPedido,
            $data['cliente_id'],
            $data['fecha'] ?? date('Y-m-d'),
            $data['estado'] ?? 'pendiente',
            $data['subtotal'] ?? 0,
            $data['descuento'] ?? 0,
            $data['impuestos'] ?? 0,
            $data['total'] ?? 0,
            $data['notas'] ?? null,
            $data['usuario_id']
        ]);

        return (int) $this->db->lastInsertId();
    }

    public function agregarLinea(int $pedidoId, array $linea): int
    {
        $sql = "INSERT INTO lineas_pedido_venta (pedido_id, producto_id, cantidad, precio_unitario, descuento, total) 
                VALUES (?, ?, ?, ?, ?, ?)";
        
        $total = ($linea['cantidad'] * $linea['precio_unitario']) * (1 - ($linea['descuento'] ?? 0) / 100);
        
        $this->db->query($sql, [
            $pedidoId,
            $linea['producto_id'],
            $linea['cantidad'],
            $linea['precio_unitario'],
            $linea['descuento'] ?? 0,
            $total
        ]);

        return (int) $this->db->lastInsertId();
    }

    public function actualizarTotales(int $pedidoId): void
    {
        $lineas = $this->getLineas($pedidoId);
        
        $subtotal = 0;
        foreach ($lineas as $linea) {
            $subtotal += $linea['total'];
        }

        $pedido = $this->db->fetchOne("SELECT descuento FROM pedidos_venta WHERE id = ?", [$pedidoId]);
        $descuento = $pedido['descuento'] ?? 0;
        $subtotalConDescuento = $subtotal - $descuento;
        $impuestos = $subtotalConDescuento * 0.21;
        $total = $subtotalConDescuento + $impuestos;

        $this->db->query(
            "UPDATE pedidos_venta SET subtotal = ?, impuestos = ?, total = ? WHERE id = ?",
            [$subtotal, $impuestos, $total, $pedidoId]
        );
    }

    private function generarNumeroPedido(): string
    {
        $year = date('Y');
        $ultimo = $this->db->fetchOne(
            "SELECT numero_pedido FROM pedidos_venta WHERE numero_pedido LIKE ? ORDER BY id DESC LIMIT 1",
            ["PED-V-$year-%"]
        );

        if ($ultimo) {
            $numero = (int) substr($ultimo['numero_pedido'], -4) + 1;
        } else {
            $numero = 1;
        }

        return sprintf("PED-V-%s-%04d", $year, $numero);
    }
}

