<?php

namespace OrionERP\Services;

use OrionERP\Models\Producto;
use OrionERP\Core\Database;

class StockService
{
    private $productoModel;
    private $db;

    public function __construct()
    {
        $this->productoModel = new Producto();
        $this->db = Database::getInstance();
    }

    public function getProductosStockBajo(): array
    {
        return $this->db->fetchAll(
            "SELECT p.*, c.nombre as categoria_nombre 
             FROM productos p
             LEFT JOIN categorias c ON p.categoria_id = c.id
             WHERE p.stock_actual <= p.stock_minimo AND p.activo = 1
             ORDER BY (p.stock_actual - p.stock_minimo) ASC"
        );
    }

    public function ajustarStock(int $productoId, int $cantidad, string $motivo, int $usuarioId): bool
    {
        $producto = $this->productoModel->findById($productoId);
        if (!$producto) {
            return false;
        }

        $nuevoStock = max(0, $producto['stock_actual'] + $cantidad);
        
        $this->db->query(
            "UPDATE productos SET stock_actual = ? WHERE id = ?",
            [$nuevoStock, $productoId]
        );

        $tipo = $cantidad > 0 ? 'entrada' : 'salida';
        $this->db->query(
            "INSERT INTO movimientos_stock (producto_id, tipo, cantidad, motivo, referencia, usuario_id) 
             VALUES (?, ?, ?, ?, ?, ?)",
            [$productoId, $tipo, abs($cantidad), $motivo, 'ajuste_manual', $usuarioId]
        );

        return true;
    }

    public function getMovimientosStock(int $productoId, int $limit = 50): array
    {
        return $this->db->fetchAll(
            "SELECT ms.*, u.nombre as usuario_nombre 
             FROM movimientos_stock ms
             LEFT JOIN usuarios u ON ms.usuario_id = u.id
             WHERE ms.producto_id = ?
             ORDER BY ms.created_at DESC
             LIMIT ?",
            [$productoId, $limit]
        );
    }
}

