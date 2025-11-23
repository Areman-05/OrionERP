<?php

namespace OrionERP\Services;

use OrionERP\Core\Database;

class RotacionService
{
    private $db;

    public function __construct()
    {
        $this->db = Database::getInstance();
    }

    public function calcularRotacion(int $productoId, int $periodoDias = 30): array
    {
        $fechaInicio = date('Y-m-d', strtotime("-$periodoDias days"));
        
        $ventas = $this->db->fetchOne(
            "SELECT SUM(lpv.cantidad) as cantidad_vendida
             FROM lineas_pedido_venta lpv
             LEFT JOIN pedidos_venta pv ON lpv.pedido_id = pv.id
             WHERE lpv.producto_id = ? 
             AND pv.fecha >= ? 
             AND pv.estado != 'cancelado'",
            [$productoId, $fechaInicio]
        );
        
        $compras = $this->db->fetchOne(
            "SELECT SUM(lpc.cantidad) as cantidad_comprada
             FROM lineas_pedido_compra lpc
             LEFT JOIN pedidos_compra pc ON lpc.pedido_id = pc.id
             WHERE lpc.producto_id = ? 
             AND pc.fecha >= ? 
             AND pc.estado != 'cancelado'",
            [$productoId, $fechaInicio]
        );
        
        $producto = $this->db->fetchOne(
            "SELECT stock_actual, precio_compra FROM productos WHERE id = ?",
            [$productoId]
        );
        
        $cantidadVendida = (int) ($ventas['cantidad_vendida'] ?? 0);
        $cantidadComprada = (int) ($compras['cantidad_comprada'] ?? 0);
        $stockActual = (int) ($producto['stock_actual'] ?? 0);
        
        $rotacion = $stockActual > 0 ? ($cantidadVendida / $stockActual) * ($periodoDias / 30) : 0;
        $diasInventario = $stockActual > 0 && $cantidadVendida > 0 ? ($stockActual / ($cantidadVendida / $periodoDias)) : 0;
        
        return [
            'producto_id' => $productoId,
            'periodo_dias' => $periodoDias,
            'cantidad_vendida' => $cantidadVendida,
            'cantidad_comprada' => $cantidadComprada,
            'stock_actual' => $stockActual,
            'rotacion' => round($rotacion, 2),
            'dias_inventario' => round($diasInventario, 0)
        ];
    }

    public function getProductosRotacionBaja(int $limit = 20): array
    {
        $productos = $this->db->fetchAll(
            "SELECT id, codigo, nombre, stock_actual FROM productos WHERE activo = 1 ORDER BY stock_actual DESC LIMIT ?",
            [$limit]
        );
        
        $resultados = [];
        foreach ($productos as $producto) {
            $rotacion = $this->calcularRotacion($producto['id']);
            if ($rotacion['rotacion'] < 1) {
                $resultados[] = array_merge($producto, $rotacion);
            }
        }
        
        return $resultados;
    }
}

