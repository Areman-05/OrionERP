<?php

namespace OrionERP\Services;

use OrionERP\Models\Categoria;
use OrionERP\Core\Database;

class CategoriaService
{
    private $categoriaModel;
    private $db;

    public function __construct()
    {
        $this->categoriaModel = new Categoria();
        $this->db = Database::getInstance();
    }

    public function getEstadisticasCategoria(int $categoriaId): array
    {
        $productos = $this->db->fetchOne(
            "SELECT COUNT(*) as total, SUM(stock_actual) as stock_total, 
                    SUM(stock_actual * precio_compra) as valor_inventario
             FROM productos 
             WHERE categoria_id = ? AND activo = 1",
            [$categoriaId]
        );

        $ventas = $this->db->fetchOne(
            "SELECT SUM(lpv.cantidad) as cantidad_vendida, SUM(lpv.subtotal) as total_ventas
             FROM lineas_pedido_venta lpv
             INNER JOIN productos p ON lpv.producto_id = p.id
             INNER JOIN pedidos_venta pv ON lpv.pedido_id = pv.id
             WHERE p.categoria_id = ? AND pv.estado != 'cancelado'",
            [$categoriaId]
        );

        return [
            'total_productos' => (int) ($productos['total'] ?? 0),
            'stock_total' => (int) ($productos['stock_total'] ?? 0),
            'valor_inventario' => (float) ($productos['valor_inventario'] ?? 0),
            'cantidad_vendida' => (int) ($ventas['cantidad_vendida'] ?? 0),
            'total_ventas' => (float) ($ventas['total_ventas'] ?? 0)
        ];
    }

    public function getCategoriasMasVendidas(int $limit = 10, string $fechaInicio = null, string $fechaFin = null): array
    {
        $where = "pv.estado != 'cancelado'";
        $params = [];

        if ($fechaInicio && $fechaFin) {
            $where .= " AND pv.fecha BETWEEN ? AND ?";
            $params = [$fechaInicio, $fechaFin];
        }

        return $this->db->fetchAll(
            "SELECT c.id, c.nombre, SUM(lpv.cantidad) as cantidad_vendida, SUM(lpv.subtotal) as total_ventas
             FROM categorias c
             INNER JOIN productos p ON c.id = p.categoria_id
             INNER JOIN lineas_pedido_venta lpv ON p.id = lpv.producto_id
             INNER JOIN pedidos_venta pv ON lpv.pedido_id = pv.id
             WHERE $where
             GROUP BY c.id, c.nombre
             ORDER BY total_ventas DESC
             LIMIT ?",
            array_merge($params, [$limit])
        );
    }
}

