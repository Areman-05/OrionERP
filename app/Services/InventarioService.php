<?php

namespace OrionERP\Services;

use OrionERP\Core\Database;

class InventarioService
{
    private $db;

    public function __construct()
    {
        $this->db = Database::getInstance();
    }

    public function getValorInventario(): float
    {
        $result = $this->db->fetchOne(
            "SELECT SUM(stock_actual * precio_compra) as valor 
             FROM productos WHERE activo = 1"
        );
        
        return (float) ($result['valor'] ?? 0);
    }

    public function getInventarioPorCategoria(): array
    {
        return $this->db->fetchAll(
            "SELECT 
                c.nombre as categoria,
                COUNT(p.id) as total_productos,
                SUM(p.stock_actual) as stock_total,
                SUM(p.stock_actual * p.precio_compra) as valor_inventario
             FROM productos p
             LEFT JOIN categorias c ON p.categoria_id = c.id
             WHERE p.activo = 1
             GROUP BY c.id, c.nombre
             ORDER BY valor_inventario DESC"
        );
    }

    public function getProductosSinStock(): array
    {
        return $this->db->fetchAll(
            "SELECT p.*, c.nombre as categoria_nombre 
             FROM productos p
             LEFT JOIN categorias c ON p.categoria_id = c.id
             WHERE p.stock_actual = 0 AND p.activo = 1
             ORDER BY p.nombre"
        );
    }

    public function getResumenInventario(): array
    {
        $totalProductos = $this->db->fetchOne(
            "SELECT COUNT(*) as total FROM productos WHERE activo = 1"
        );
        
        $productosStockBajo = $this->db->fetchOne(
            "SELECT COUNT(*) as total FROM productos 
             WHERE stock_actual <= stock_minimo AND activo = 1"
        );
        
        $productosSinStock = $this->db->fetchOne(
            "SELECT COUNT(*) as total FROM productos 
             WHERE stock_actual = 0 AND activo = 1"
        );
        
        $valorInventario = $this->getValorInventario();
        
        return [
            'total_productos' => (int) ($totalProductos['total'] ?? 0),
            'productos_stock_bajo' => (int) ($productosStockBajo['total'] ?? 0),
            'productos_sin_stock' => (int) ($productosSinStock['total'] ?? 0),
            'valor_inventario' => $valorInventario
        ];
    }
}

