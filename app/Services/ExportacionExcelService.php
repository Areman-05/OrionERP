<?php

namespace OrionERP\Services;

use OrionERP\Core\Database;

class ExportacionExcelService
{
    private $db;

    public function __construct()
    {
        $this->db = Database::getInstance();
    }

    public function exportarProductosExcel(): string
    {
        $productos = $this->db->fetchAll(
            "SELECT p.codigo, p.nombre, c.nombre as categoria, p.precio_venta, p.precio_compra, 
                    p.stock_actual, p.stock_minimo, p.activo
             FROM productos p
             LEFT JOIN categorias c ON p.categoria_id = c.id
             ORDER BY p.nombre"
        );
        
        return $this->generarCSV($productos, 'productos');
    }

    public function exportarVentasExcel(string $fechaInicio, string $fechaFin): string
    {
        $ventas = $this->db->fetchAll(
            "SELECT pv.numero_pedido, c.nombre as cliente, pv.fecha, pv.estado, 
                    pv.subtotal, pv.impuestos, pv.total
             FROM pedidos_venta pv
             LEFT JOIN clientes c ON pv.cliente_id = c.id
             WHERE pv.fecha BETWEEN ? AND ?
             ORDER BY pv.fecha DESC",
            [$fechaInicio, $fechaFin]
        );
        
        return $this->generarCSV($ventas, 'ventas');
    }

    private function generarCSV(array $datos, string $nombre): string
    {
        if (empty($datos)) {
            return '';
        }
        
        $output = fopen('php://temp', 'r+');
        
        // BOM para UTF-8 (Excel)
        fprintf($output, chr(0xEF).chr(0xBB).chr(0xBF));
        
        // Encabezados
        fputcsv($output, array_keys($datos[0]), ';');
        
        // Datos
        foreach ($datos as $fila) {
            fputcsv($output, $fila, ';');
        }
        
        rewind($output);
        $csv = stream_get_contents($output);
        fclose($output);
        
        return $csv;
    }
}


