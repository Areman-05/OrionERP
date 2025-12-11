<?php

namespace OrionERP\Services;

use OrionERP\Models\Factura;
use OrionERP\Models\PedidoVenta;
use OrionERP\Models\LineaFactura;
use OrionERP\Services\PdfService;

class FacturacionService
{
    private $facturaModel;
    private $pedidoModel;
    private $lineaModel;
    private $pdfService;

    public function __construct()
    {
        $this->facturaModel = new Factura();
        $this->pedidoModel = new PedidoVenta();
        $this->lineaModel = new LineaFactura();
        $this->pdfService = new PdfService();
    }

    public function generarFacturaDesdePedido(int $pedidoId): int
    {
        $pedido = $this->pedidoModel->findById($pedidoId);
        if (!$pedido) {
            throw new \Exception('Pedido no encontrado');
        }

        if ($pedido['estado'] === 'cancelado') {
            throw new \Exception('No se puede facturar un pedido cancelado');
        }

        // Crear factura
        $facturaData = [
            'pedido_id' => $pedidoId,
            'cliente_id' => $pedido['cliente_id'],
            'fecha_emision' => date('Y-m-d'),
            'fecha_vencimiento' => date('Y-m-d', strtotime('+30 days')),
            'estado' => 'pendiente',
            'subtotal' => $pedido['subtotal'],
            'impuestos' => $pedido['impuestos'],
            'total' => $pedido['total']
        ];

        $facturaId = $this->facturaModel->create($facturaData);

        // Copiar líneas del pedido a la factura
        foreach ($pedido['lineas'] as $lineaPedido) {
            $this->lineaModel->create([
                'factura_id' => $facturaId,
                'producto_id' => $lineaPedido['producto_id'],
                'descripcion' => $lineaPedido['producto_nombre'] ?? 'Producto',
                'cantidad' => $lineaPedido['cantidad'],
                'precio_unitario' => $lineaPedido['precio_unitario'],
                'descuento' => $lineaPedido['descuento'],
                'impuesto' => 21
            ]);
        }

        // Generar PDF
        $archivoPdf = $this->pdfService->generarFactura($facturaId);
        
        // Actualizar factura con ruta del PDF
        $this->facturaModel->update($facturaId, ['archivo_pdf' => $archivoPdf]);

        // Actualizar estado del pedido
        $this->pedidoModel->update($pedidoId, ['estado' => 'pagado']);

        return $facturaId;
    }

    public function marcarFacturaPagada(int $facturaId): bool
    {
        $factura = $this->facturaModel->findById($facturaId);
        if (!$factura) {
            return false;
        }

        $this->facturaModel->update($facturaId, ['estado' => 'pagada']);
        return true;
    }

    public function getFacturasPendientes(): array
    {
        return $this->facturaModel->getAll(['estado' => 'pendiente']);
    }

    public function getFacturasVencidas(): array
    {
        $db = \OrionERP\Core\Database::getInstance();
        return $db->fetchAll(
            "SELECT f.*, c.nombre as cliente_nombre, c.email as cliente_email
             FROM facturas f
             LEFT JOIN clientes c ON f.cliente_id = c.id
             WHERE f.estado = 'pendiente' 
             AND f.fecha_vencimiento < CURDATE()
             ORDER BY f.fecha_vencimiento ASC"
        );
    }

    public function calcularEstadisticasFacturacion(string $fechaInicio, string $fechaFin): array
    {
        $db = \OrionERP\Core\Database::getInstance();
        
        $estadisticas = $db->fetchOne(
            "SELECT 
                COUNT(*) as total_facturas,
                SUM(CASE WHEN estado = 'pagada' THEN total ELSE 0 END) as total_pagado,
                SUM(CASE WHEN estado = 'pendiente' THEN total ELSE 0 END) as total_pendiente,
                SUM(CASE WHEN estado = 'vencida' THEN total ELSE 0 END) as total_vencido,
                AVG(total) as promedio_factura
             FROM facturas
             WHERE fecha_emision BETWEEN ? AND ?",
            [$fechaInicio, $fechaFin]
        );

        return [
            'total_facturas' => (int) ($estadisticas['total_facturas'] ?? 0),
            'total_pagado' => (float) ($estadisticas['total_pagado'] ?? 0),
            'total_pendiente' => (float) ($estadisticas['total_pendiente'] ?? 0),
            'total_vencido' => (float) ($estadisticas['total_vencido'] ?? 0),
            'promedio_factura' => (float) ($estadisticas['promedio_factura'] ?? 0)
        ];
    }
}

