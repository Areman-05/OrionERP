<?php

namespace OrionERP\Services;

use OrionERP\Models\Producto;
use OrionERP\Core\Database;

class ProductoService
{
    private $productoModel;
    private $db;

    public function __construct()
    {
        $this->productoModel = new Producto();
        $this->db = Database::getInstance();
    }

    public function getProductosMasVendidos(int $limit = 10, string $fechaInicio = null, string $fechaFin = null): array
    {
        $where = "pv.estado != 'cancelado'";
        $params = [];

        if ($fechaInicio && $fechaFin) {
            $where .= " AND pv.fecha BETWEEN ? AND ?";
            $params = [$fechaInicio, $fechaFin];
        }

        return $this->db->fetchAll(
            "SELECT p.id, p.nombre, p.codigo, SUM(lpv.cantidad) as cantidad_vendida, SUM(lpv.subtotal) as total_ventas
             FROM productos p
             INNER JOIN lineas_pedido_venta lpv ON p.id = lpv.producto_id
             INNER JOIN pedidos_venta pv ON lpv.pedido_id = pv.id
             WHERE $where
             GROUP BY p.id, p.nombre, p.codigo
             ORDER BY cantidad_vendida DESC
             LIMIT ?",
            array_merge($params, [$limit])
        );
    }

    public function getProductosStockBajo(): array
    {
        return $this->db->fetchAll(
            "SELECT * FROM productos 
             WHERE stock_actual <= stock_minimo AND activo = 1
             ORDER BY (stock_actual - stock_minimo) ASC"
        );
    }

    public function buscarProductos(string $termino): array
    {
        $termino = "%$termino%";
        return $this->db->fetchAll(
            "SELECT * FROM productos 
             WHERE (nombre LIKE ? OR codigo LIKE ? OR descripcion LIKE ?) AND activo = 1
             ORDER BY nombre ASC
             LIMIT 50",
            [$termino, $termino, $termino]
        );
    }
}
