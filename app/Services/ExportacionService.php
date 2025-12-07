<?php

namespace OrionERP\Services;

use OrionERP\Core\Database;

class ExportacionService
{
    private $db;

    public function __construct()
    {
        $this->db = Database::getInstance();
    }

    public function exportarProductos(): string
    {
        $productos = $this->db->fetchAll(
            "SELECT p.codigo, p.nombre, c.nombre as categoria, p.precio_venta, p.precio_compra, p.stock_actual, p.stock_minimo, p.activo
             FROM productos p
             LEFT JOIN categorias c ON p.categoria_id = c.id
             ORDER BY p.nombre"
        );
        
        return $this->arrayToCsv($productos);
    }

    public function exportarClientes(): string
    {
        $clientes = $this->db->fetchAll(
            "SELECT codigo, nombre, tipo_documento, numero_documento, email, telefono, ciudad, estado
             FROM clientes
             ORDER BY nombre"
        );
        
        return $this->arrayToCsv($clientes);
    }

    public function exportarVentas(string $fechaInicio, string $fechaFin): string
    {
        $ventas = $this->db->fetchAll(
            "SELECT pv.numero_pedido, c.nombre as cliente, pv.fecha, pv.estado, pv.total
             FROM pedidos_venta pv
             LEFT JOIN clientes c ON pv.cliente_id = c.id
             WHERE pv.fecha BETWEEN ? AND ?
             ORDER BY pv.fecha DESC",
            [$fechaInicio, $fechaFin]
        );
        
        return $this->arrayToCsv($ventas);
    }

    public function exportarInventario(): string
    {
        $inventario = $this->db->fetchAll(
            "SELECT p.codigo, p.nombre, c.nombre as categoria, 
                    p.stock_actual, p.stock_minimo, 
                    p.precio_compra, (p.stock_actual * p.precio_compra) as valor_inventario
             FROM productos p
             LEFT JOIN categorias c ON p.categoria_id = c.id
             WHERE p.activo = 1
             ORDER BY p.nombre"
        );
        
        return $this->arrayToCsv($inventario);
    }

    private function arrayToCsv(array $data): string
    {
        if (empty($data)) {
            return '';
        }
        
        $output = fopen('php://temp', 'r+');
        
        // Encabezados
        fputcsv($output, array_keys($data[0]));
        
        // Datos
        foreach ($data as $row) {
            fputcsv($output, $row);
        }
        
        rewind($output);
        $csv = stream_get_contents($output);
        fclose($output);
        
        return $csv;
    }
}
