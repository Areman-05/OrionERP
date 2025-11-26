<?php

namespace OrionERP\Services;

use Dompdf\Dompdf;
use Dompdf\Options;
use OrionERP\Models\Factura;
use OrionERP\Models\Cliente;
use OrionERP\Models\LineaFactura;
use OrionERP\Models\ConfiguracionEmpresa;

class PdfService
{
    private $dompdf;
    private $config;

    public function __construct()
    {
        $options = new Options();
        $options->set('isHtml5ParserEnabled', true);
        $options->set('isRemoteEnabled', true);
        $options->set('defaultFont', 'Arial');
        
        $this->dompdf = new Dompdf($options);
        $this->config = new ConfiguracionEmpresa();
    }

    public function generarFactura(int $facturaId): string
    {
        $facturaModel = new Factura();
        $clienteModel = new Cliente();
        $lineaModel = new LineaFactura();
        
        $factura = $facturaModel->findById($facturaId);
        if (!$factura) {
            throw new \Exception('Factura no encontrada');
        }
        
        $cliente = $clienteModel->findById($factura['cliente_id']);
        $lineas = $lineaModel->getByFactura($facturaId);
        
        $configEmpresa = $this->config->getAll();
        
        $html = $this->generarHtmlFactura($factura, $cliente, $lineas, $configEmpresa);
        
        $this->dompdf->loadHtml($html);
        $this->dompdf->setPaper('A4', 'portrait');
        $this->dompdf->render();
        
        $outputDir = __DIR__ . '/../../public/facturas/';
        if (!is_dir($outputDir)) {
            mkdir($outputDir, 0755, true);
        }
        
        $filename = 'factura_' . $factura['numero_factura'] . '.pdf';
        $filepath = $outputDir . $filename;
        
        file_put_contents($filepath, $this->dompdf->output());
        
        return $filepath;
    }

    private function generarHtmlFactura(array $factura, array $cliente, array $lineas, array $config): string
    {
        $html = '<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <style>
        body { font-family: Arial, sans-serif; font-size: 12px; }
        .header { margin-bottom: 30px; }
        .empresa { float: left; width: 50%; }
        .cliente { float: right; width: 45%; }
        .clear { clear: both; }
        table { width: 100%; border-collapse: collapse; margin: 20px 0; }
        th, td { border: 1px solid #ddd; padding: 8px; text-align: left; }
        th { background-color: #f2f2f2; }
        .total { text-align: right; font-weight: bold; }
        .footer { margin-top: 30px; font-size: 10px; }
    </style>
</head>
<body>
    <div class="header">
        <div class="empresa">
            <h2>' . htmlspecialchars($config['nombre_empresa'] ?? 'OrionERP') . '</h2>
            <p>' . htmlspecialchars($config['direccion'] ?? '') . '</p>
            <p>CIF: ' . htmlspecialchars($config['cif'] ?? '') . '</p>
        </div>
        <div class="cliente">
            <h3>Cliente:</h3>
            <p><strong>' . htmlspecialchars($cliente['nombre']) . '</strong></p>
            <p>' . htmlspecialchars($cliente['direccion'] ?? '') . '</p>
            <p>' . htmlspecialchars($cliente['ciudad'] ?? '') . '</p>
        </div>
        <div class="clear"></div>
    </div>
    
    <h2>FACTURA: ' . htmlspecialchars($factura['numero_factura']) . '</h2>
    <p><strong>Fecha:</strong> ' . $factura['fecha_emision'] . '</p>';
        
        if ($factura['fecha_vencimiento']) {
            $html .= '<p><strong>Vencimiento:</strong> ' . $factura['fecha_vencimiento'] . '</p>';
        }
        
        $html .= '<table>
        <thead>
            <tr>
                <th>Descripción</th>
                <th>Cantidad</th>
                <th>Precio Unit.</th>
                <th>Total</th>
            </tr>
        </thead>
        <tbody>';
        
        foreach ($lineas as $linea) {
            $html .= '<tr>
                <td>' . htmlspecialchars($linea['descripcion']) . '</td>
                <td>' . $linea['cantidad'] . '</td>
                <td>' . number_format($linea['precio_unitario'], 2) . ' €</td>
                <td>' . number_format($linea['total'], 2) . ' €</td>
            </tr>';
        }
        
        $html .= '</tbody>
        <tfoot>
            <tr>
                <td colspan="3" class="total">Subtotal:</td>
                <td class="total">' . number_format($factura['subtotal'], 2) . ' €</td>
            </tr>
            <tr>
                <td colspan="3" class="total">IVA:</td>
                <td class="total">' . number_format($factura['impuestos'], 2) . ' €</td>
            </tr>
            <tr>
                <td colspan="3" class="total">TOTAL:</td>
                <td class="total">' . number_format($factura['total'], 2) . ' €</td>
            </tr>
        </tfoot>
    </table>
    
    <div class="footer">
        <p>Gracias por su confianza</p>
    </div>
</body>
</html>';
        
        return $html;
    }
}

