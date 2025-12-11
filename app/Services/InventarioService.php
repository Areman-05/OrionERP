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

    public function generarConteoFisico(array $productos): array
    {
        $diferencias = [];
        
        foreach ($productos as $item) {
            $producto = $this->db->fetchOne(
                "SELECT id, nombre, stock_actual FROM productos WHERE id = ?",
                [$item['producto_id']]
            );
            
            if ($producto) {
                $diferencia = $item['cantidad_fisica'] - $producto['stock_actual'];
                if ($diferencia != 0) {
                    $diferencias[] = [
                        'producto_id' => $producto['id'],
                        'producto_nombre' => $producto['nombre'],
                        'stock_sistema' => $producto['stock_actual'],
                        'stock_fisico' => $item['cantidad_fisica'],
                        'diferencia' => $diferencia
                    ];
                }
            }
        }
        
        return $diferencias;
    }

    public function getRotacionProductos(int $meses = 6): array
    {
        return $this->db->fetchAll(
            "SELECT 
                p.id,
                p.nombre,
                p.codigo,
                p.stock_actual,
                COALESCE(SUM(lpv.cantidad), 0) as cantidad_vendida,
                CASE 
                    WHEN p.stock_actual > 0 
                    THEN COALESCE(SUM(lpv.cantidad), 0) / p.stock_actual 
                    ELSE 0 
                END as rotacion
             FROM productos p
             LEFT JOIN lineas_pedido_venta lpv ON p.id = lpv.producto_id
             LEFT JOIN pedidos_venta pv ON lpv.pedido_id = pv.id
             WHERE p.activo = 1 
             AND (pv.fecha IS NULL OR pv.fecha >= DATE_SUB(CURDATE(), INTERVAL ? MONTH))
             GROUP BY p.id, p.nombre, p.codigo, p.stock_actual
             ORDER BY rotacion DESC",
            [$meses]
        );
    }

    public function getMovimientosInventario(int $productoId = null, string $fechaInicio = null, string $fechaFin = null): array
    {
        $where = "1=1";
        $params = [];

        if ($productoId) {
            $where .= " AND ms.producto_id = ?";
            $params[] = $productoId;
        }

        if ($fechaInicio && $fechaFin) {
            $where .= " AND ms.fecha BETWEEN ? AND ?";
            $params[] = $fechaInicio;
            $params[] = $fechaFin;
        }

        return $this->db->fetchAll(
            "SELECT ms.*, p.nombre as producto_nombre, u.nombre as usuario_nombre
             FROM movimientos_stock ms
             LEFT JOIN productos p ON ms.producto_id = p.id
             LEFT JOIN usuarios u ON ms.usuario_id = u.id
             WHERE $where
             ORDER BY ms.fecha DESC, ms.id DESC
             LIMIT 100",
            $params
        );
    }

    public function calcularValorInventarioPorAlmacen(int $almacenId = null): float
    {
        $where = "p.activo = 1";
        $params = [];

        if ($almacenId) {
            $where .= " AND p.almacen_id = ?";
            $params[] = $almacenId;
        }

        $result = $this->db->fetchOne(
            "SELECT SUM(p.stock_actual * p.precio_compra) as valor 
             FROM productos p
             WHERE $where",
            $params
        );
        
        return (float) ($result['valor'] ?? 0);
    }
}
