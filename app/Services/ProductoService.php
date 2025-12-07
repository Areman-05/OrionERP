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

    public function getProductosPorCategoria(int $categoriaId): array
    {
        return $this->db->fetchAll(
            "SELECT p.*, c.nombre as categoria_nombre 
             FROM productos p
             LEFT JOIN categorias c ON p.categoria_id = c.id
             WHERE p.categoria_id = ? AND p.activo = 1
             ORDER BY p.nombre ASC"
        );
    }

    public function actualizarPreciosMasivos(array $productos, float $porcentaje, string $tipo = 'aumento'): int
    {
        $actualizados = 0;
        $this->db->beginTransaction();

        try {
            foreach ($productos as $productoId) {
                $producto = $this->productoModel->findById($productoId);
                if ($producto) {
                    $precioActual = (float) $producto['precio_venta'];
                    $nuevoPrecio = $tipo === 'aumento' 
                        ? $precioActual * (1 + $porcentaje / 100)
                        : $precioActual * (1 - $porcentaje / 100);

                    $this->db->query(
                        "UPDATE productos SET precio_venta = ? WHERE id = ?",
                        [$nuevoPrecio, $productoId]
                    );
                    $actualizados++;
                }
            }

            $this->db->commit();
            return $actualizados;
        } catch (\Exception $e) {
            $this->db->rollBack();
            throw $e;
        }
    }
}
