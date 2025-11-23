<?php

namespace OrionERP\Models;

use OrionERP\Core\Database;

class PedidoCompra
{
    private $db;

    public function __construct()
    {
        $this->db = Database::getInstance();
    }

    public function getAll(): array
    {
        return $this->db->fetchAll(
            "SELECT pc.*, p.nombre as proveedor_nombre, u.nombre as usuario_nombre
             FROM pedidos_compra pc
             LEFT JOIN proveedores p ON pc.proveedor_id = p.id
             LEFT JOIN usuarios u ON pc.usuario_id = u.id
             ORDER BY pc.fecha DESC, pc.created_at DESC"
        );
    }

    public function findById(int $id): ?array
    {
        $pedido = $this->db->fetchOne(
            "SELECT pc.*, p.nombre as proveedor_nombre, u.nombre as usuario_nombre
             FROM pedidos_compra pc
             LEFT JOIN proveedores p ON pc.proveedor_id = p.id
             LEFT JOIN usuarios u ON pc.usuario_id = u.id
             WHERE pc.id = ?",
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
            "SELECT lpc.*, pr.nombre as producto_nombre, pr.codigo as producto_codigo
             FROM lineas_pedido_compra lpc
             LEFT JOIN productos pr ON lpc.producto_id = pr.id
             WHERE lpc.pedido_id = ?",
            [$pedidoId]
        );
    }

    public function create(array $data): int
    {
        $numeroPedido = $this->generarNumeroPedido();
        
        $sql = "INSERT INTO pedidos_compra (numero_pedido, proveedor_id, fecha, fecha_entrega_esperada, estado, subtotal, descuento, impuestos, total, notas, usuario_id) 
                VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";
        
        $this->db->query($sql, [
            $numeroPedido,
            $data['proveedor_id'],
            $data['fecha'] ?? date('Y-m-d'),
            $data['fecha_entrega_esperada'] ?? null,
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

    public function update(int $id, array $data): bool
    {
        $sql = "UPDATE pedidos_compra SET estado = ?, fecha_entrega_esperada = ?, notas = ? WHERE id = ?";
        
        $this->db->query($sql, [
            $data['estado'] ?? 'pendiente',
            $data['fecha_entrega_esperada'] ?? null,
            $data['notas'] ?? null,
            $id
        ]);

        return true;
    }

    public function agregarLinea(int $pedidoId, array $linea): int
    {
        $sql = "INSERT INTO lineas_pedido_compra (pedido_id, producto_id, cantidad, cantidad_recibida, precio_unitario, descuento, total) 
                VALUES (?, ?, ?, ?, ?, ?, ?)";
        
        $total = ($linea['cantidad'] * $linea['precio_unitario']) * (1 - ($linea['descuento'] ?? 0) / 100);
        
        $this->db->query($sql, [
            $pedidoId,
            $linea['producto_id'],
            $linea['cantidad'],
            $linea['cantidad_recibida'] ?? 0,
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

        $pedido = $this->db->fetchOne("SELECT descuento FROM pedidos_compra WHERE id = ?", [$pedidoId]);
        $descuento = $pedido['descuento'] ?? 0;
        $subtotalConDescuento = $subtotal - $descuento;
        $impuestos = $subtotalConDescuento * 0.21; // IVA 21%
        $total = $subtotalConDescuento + $impuestos;

        $this->db->query(
            "UPDATE pedidos_compra SET subtotal = ?, impuestos = ?, total = ? WHERE id = ?",
            [$subtotal, $impuestos, $total, $pedidoId]
        );
    }

    public function recibirPedido(int $pedidoId, array $lineasRecibidas, int $usuarioId): bool
    {
        $pedido = $this->findById($pedidoId);
        if (!$pedido) {
            return false;
        }

        $productoModel = new Producto();
        $todasCompletas = true;
        $algunasRecibidas = false;

        foreach ($lineasRecibidas as $lineaRecibida) {
            $lineaId = $lineaRecibida['linea_id'];
            $cantidadRecibida = (int) $lineaRecibida['cantidad_recibida'];
            
            if ($cantidadRecibida > 0) {
                $algunasRecibidas = true;
                
                // Actualizar cantidad recibida
                $linea = $this->db->fetchOne(
                    "SELECT * FROM lineas_pedido_compra WHERE id = ? AND pedido_id = ?",
                    [$lineaId, $pedidoId]
                );
                
                if ($linea) {
                    $nuevaCantidadRecibida = $linea['cantidad_recibida'] + $cantidadRecibida;
                    $this->db->query(
                        "UPDATE lineas_pedido_compra SET cantidad_recibida = ? WHERE id = ?",
                        [$nuevaCantidadRecibida, $lineaId]
                    );

                    // Actualizar stock del producto
                    $productoModel->incrementarStock($linea['producto_id'], $cantidadRecibida, 'entrada', "Pedido compra: {$pedido['numero_pedido']}", $usuarioId);

                    // Verificar si la línea está completa
                    if ($nuevaCantidadRecibida < $linea['cantidad']) {
                        $todasCompletas = false;
                    }
                }
            }
        }

        // Actualizar estado del pedido
        if ($todasCompletas && $algunasRecibidas) {
            $nuevoEstado = 'completado';
        } elseif ($algunasRecibidas) {
            $nuevoEstado = 'parcial';
        } else {
            $nuevoEstado = $pedido['estado'];
        }

        $this->db->query(
            "UPDATE pedidos_compra SET estado = ? WHERE id = ?",
            [$nuevoEstado, $pedidoId]
        );

        return true;
    }

    private function generarNumeroPedido(): string
    {
        $year = date('Y');
        $ultimo = $this->db->fetchOne(
            "SELECT numero_pedido FROM pedidos_compra WHERE numero_pedido LIKE ? ORDER BY id DESC LIMIT 1",
            ["PED-C-$year-%"]
        );

        if ($ultimo) {
            $numero = (int) substr($ultimo['numero_pedido'], -4) + 1;
        } else {
            $numero = 1;
        }

        return sprintf("PED-C-%s-%04d", $year, $numero);
    }
}

