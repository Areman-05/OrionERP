<?php

namespace OrionERP\Services;

use OrionERP\Core\Database;

class ReporteService
{
    private $db;

    public function __construct()
    {
        $this->db = Database::getInstance();
    }

    public function generarReporteVentas(string $fechaInicio, string $fechaFin): array
    {
        $ventas = $this->db->fetchAll(
            "SELECT 
                pv.id,
                pv.numero_pedido,
                pv.fecha,
                c.nombre as cliente,
                pv.total,
                pv.estado,
                u.nombre as vendedor
             FROM pedidos_venta pv
             LEFT JOIN clientes c ON pv.cliente_id = c.id
             LEFT JOIN usuarios u ON pv.usuario_id = u.id
             WHERE pv.fecha BETWEEN ? AND ?
             ORDER BY pv.fecha DESC",
            [$fechaInicio, $fechaFin]
        );

        $resumen = $this->db->fetchOne(
            "SELECT 
                COUNT(*) as total_pedidos,
                SUM(total) as total_ventas,
                SUM(CASE WHEN estado = 'completado' THEN total ELSE 0 END) as ventas_completadas,
                SUM(CASE WHEN estado = 'pendiente' THEN total ELSE 0 END) as ventas_pendientes
             FROM pedidos_venta
             WHERE fecha BETWEEN ? AND ?",
            [$fechaInicio, $fechaFin]
        );

        return [
            'ventas' => $ventas,
            'resumen' => $resumen
        ];
    }

    public function generarReporteInventario(): array
    {
        $productos = $this->db->fetchAll(
            "SELECT 
                p.codigo,
                p.nombre,
                c.nombre as categoria,
                p.stock_actual,
                p.stock_minimo,
                p.precio_compra,
                (p.stock_actual * p.precio_compra) as valor_inventario,
                CASE 
                    WHEN p.stock_actual <= p.stock_minimo THEN 'bajo'
                    WHEN p.stock_actual <= (p.stock_minimo * 1.5) THEN 'medio'
                    ELSE 'normal'
                END as estado_stock
             FROM productos p
             LEFT JOIN categorias c ON p.categoria_id = c.id
             WHERE p.activo = 1
             ORDER BY p.nombre"
        );

        $resumen = $this->db->fetchOne(
            "SELECT 
                COUNT(*) as total_productos,
                SUM(stock_actual) as stock_total,
                SUM(stock_actual * precio_compra) as valor_total,
                SUM(CASE WHEN stock_actual <= stock_minimo THEN 1 ELSE 0 END) as productos_stock_bajo
             FROM productos
             WHERE activo = 1"
        );

        return [
            'productos' => $productos,
            'resumen' => $resumen
        ];
    }

    public function generarReporteClientes(): array
    {
        $clientes = $this->db->fetchAll(
            "SELECT 
                c.id,
                c.codigo,
                c.nombre,
                c.email,
                c.telefono,
                c.estado,
                COUNT(pv.id) as total_pedidos,
                COALESCE(SUM(pv.total), 0) as total_compras,
                MAX(pv.fecha) as ultima_compra
             FROM clientes c
             LEFT JOIN pedidos_venta pv ON c.id = pv.cliente_id AND pv.estado != 'cancelado'
             GROUP BY c.id, c.codigo, c.nombre, c.email, c.telefono, c.estado
             ORDER BY total_compras DESC"
        );

        $resumen = $this->db->fetchOne(
            "SELECT 
                COUNT(*) as total_clientes,
                SUM(CASE WHEN estado = 'activo' THEN 1 ELSE 0 END) as clientes_activos,
                SUM(CASE WHEN estado = 'inactivo' THEN 1 ELSE 0 END) as clientes_inactivos
             FROM clientes"
        );

        return [
            'clientes' => $clientes,
            'resumen' => $resumen
        ];
    }
}
