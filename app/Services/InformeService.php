<?php

namespace OrionERP\Services;

use OrionERP\Core\Database;

class InformeService
{
    private $db;

    public function __construct()
    {
        $this->db = Database::getInstance();
    }

    public function informeVentasMensual(string $mes, string $ano): array
    {
        return $this->db->fetchAll(
            "SELECT 
                pv.numero_pedido,
                c.nombre as cliente,
                pv.fecha,
                pv.total,
                pv.estado,
                u.nombre as vendedor
             FROM pedidos_venta pv
             LEFT JOIN clientes c ON pv.cliente_id = c.id
             LEFT JOIN usuarios u ON pv.usuario_id = u.id
             WHERE MONTH(pv.fecha) = ? AND YEAR(pv.fecha) = ?
             ORDER BY pv.fecha DESC",
            [$mes, $ano]
        );
    }

    public function informeGastos(string $fechaInicio, string $fechaFin): array
    {
        return $this->db->fetchAll(
            "SELECT 
                pc.numero_pedido,
                pr.nombre as proveedor,
                pc.fecha,
                pc.total,
                pc.estado
             FROM pedidos_compra pc
             LEFT JOIN proveedores pr ON pc.proveedor_id = pr.id
             WHERE pc.fecha BETWEEN ? AND ?
             ORDER BY pc.fecha DESC",
            [$fechaInicio, $fechaFin]
        );
    }

    public function informeStock(): array
    {
        return $this->db->fetchAll(
            "SELECT 
                p.codigo,
                p.nombre,
                c.nombre as categoria,
                p.stock_actual,
                p.stock_minimo,
                p.precio_compra,
                (p.stock_actual * p.precio_compra) as valor_inventario
             FROM productos p
             LEFT JOIN categorias c ON p.categoria_id = c.id
             WHERE p.activo = 1
             ORDER BY p.nombre"
        );
    }
}

