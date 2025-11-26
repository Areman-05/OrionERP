<?php

namespace OrionERP\Services;

use Dompdf\Dompdf;
use Dompdf\Options;
use OrionERP\Core\Database;

class ReportePdfService
{
    private $dompdf;
    private $db;

    public function __construct()
    {
        $options = new Options();
        $options->set('isHtml5ParserEnabled', true);
        $options->set('defaultFont', 'Arial');
        
        $this->dompdf = new Dompdf($options);
        $this->db = Database::getInstance();
    }

    public function generarReporteVentas(string $fechaInicio, string $fechaFin): string
    {
        $ventas = $this->db->fetchAll(
            "SELECT pv.*, c.nombre as cliente_nombre 
             FROM pedidos_venta pv
             LEFT JOIN clientes c ON pv.cliente_id = c.id
             WHERE pv.fecha BETWEEN ? AND ? AND pv.estado != 'cancelado'
             ORDER BY pv.fecha DESC",
            [$fechaInicio, $fechaFin]
        );
        
        $total = array_sum(array_column($ventas, 'total'));
        
        $html = $this->generarHtmlReporte('Reporte de Ventas', $ventas, [
            'Fecha Inicio' => $fechaInicio,
            'Fecha Fin' => $fechaFin,
            'Total Ventas' => number_format($total, 2) . ' €',
            'Cantidad Pedidos' => count($ventas)
        ], ['Fecha', 'Cliente', 'Número Pedido', 'Estado', 'Total']);
        
        return $this->generarPdf($html, 'reporte_ventas_' . date('Y-m-d') . '.pdf');
    }

    public function generarReporteStock(): string
    {
        $productos = $this->db->fetchAll(
            "SELECT p.codigo, p.nombre, c.nombre as categoria, p.stock_actual, p.stock_minimo, 
                    p.precio_compra, (p.stock_actual * p.precio_compra) as valor_inventario
             FROM productos p
             LEFT JOIN categorias c ON p.categoria_id = c.id
             WHERE p.activo = 1
             ORDER BY p.nombre"
        );
        
        $valorTotal = array_sum(array_column($productos, 'valor_inventario'));
        
        $html = $this->generarHtmlReporte('Reporte de Inventario', $productos, [
            'Total Productos' => count($productos),
            'Valor Total Inventario' => number_format($valorTotal, 2) . ' €'
        ], ['Código', 'Nombre', 'Categoría', 'Stock Actual', 'Stock Mínimo', 'Valor']);
        
        return $this->generarPdf($html, 'reporte_stock_' . date('Y-m-d') . '.pdf');
    }

    private function generarHtmlReporte(string $titulo, array $datos, array $resumen, array $columnas): string
    {
        $html = '<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <style>
        body { font-family: Arial, sans-serif; font-size: 11px; }
        h1 { color: #333; }
        .resumen { background-color: #f5f5f5; padding: 15px; margin: 20px 0; border-radius: 5px; }
        .resumen p { margin: 5px 0; }
        table { width: 100%; border-collapse: collapse; margin: 20px 0; }
        th, td { border: 1px solid #ddd; padding: 6px; text-align: left; }
        th { background-color: #4CAF50; color: white; }
        tr:nth-child(even) { background-color: #f2f2f2; }
        .footer { margin-top: 30px; font-size: 9px; color: #666; }
    </style>
</head>
<body>
    <h1>' . htmlspecialchars($titulo) . '</h1>
    <p><strong>Fecha de generación:</strong> ' . date('d/m/Y H:i:s') . '</p>
    
    <div class="resumen">';
        
        foreach ($resumen as $key => $value) {
            $html .= '<p><strong>' . htmlspecialchars($key) . ':</strong> ' . htmlspecialchars($value) . '</p>';
        }
        
        $html .= '</div>
    
    <table>
        <thead>
            <tr>';
        
        foreach ($columnas as $columna) {
            $html .= '<th>' . htmlspecialchars($columna) . '</th>';
        }
        
        $html .= '</tr>
        </thead>
        <tbody>';
        
        foreach ($datos as $fila) {
            $html .= '<tr>';
            foreach ($fila as $valor) {
                $html .= '<td>' . htmlspecialchars($valor ?? '') . '</td>';
            }
            $html .= '</tr>';
        }
        
        $html .= '</tbody>
    </table>
    
    <div class="footer">
        <p>Generado por OrionERP</p>
    </div>
</body>
</html>';
        
        return $html;
    }

    private function generarPdf(string $html, string $filename): string
    {
        $this->dompdf->loadHtml($html);
        $this->dompdf->setPaper('A4', 'landscape');
        $this->dompdf->render();
        
        $outputDir = __DIR__ . '/../../public/reportes/';
        if (!is_dir($outputDir)) {
            mkdir($outputDir, 0755, true);
        }
        
        $filepath = $outputDir . $filename;
        file_put_contents($filepath, $this->dompdf->output());
        
        return $filepath;
    }
}

