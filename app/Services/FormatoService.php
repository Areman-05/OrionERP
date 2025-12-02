<?php

namespace OrionERP\Services;

use OrionERP\Utils\NumberHelper;
use OrionERP\Utils\DateHelper;

class FormatoService
{
    public function formatearFactura(array $factura): array
    {
        return [
            'numero' => $factura['numero_factura'],
            'fecha' => DateHelper::formatearFecha($factura['fecha_emision']),
            'cliente' => $factura['cliente_nombre'] ?? '',
            'total' => NumberHelper::formatearMoneda($factura['total']),
            'estado' => $this->formatearEstadoFactura($factura['estado'])
        ];
    }

    public function formatearPedido(array $pedido): array
    {
        return [
            'numero' => $pedido['numero_pedido'],
            'fecha' => DateHelper::formatearFecha($pedido['fecha']),
            'cliente' => $pedido['cliente_nombre'] ?? '',
            'total' => NumberHelper::formatearMoneda($pedido['total']),
            'estado' => $this->formatearEstadoPedido($pedido['estado'])
        ];
    }

    private function formatearEstadoFactura(string $estado): string
    {
        $estados = [
            'pendiente' => 'Pendiente',
            'pagada' => 'Pagada',
            'vencida' => 'Vencida',
            'cancelada' => 'Cancelada'
        ];
        
        return $estados[$estado] ?? $estado;
    }

    private function formatearEstadoPedido(string $estado): string
    {
        $estados = [
            'pendiente' => 'Pendiente',
            'pagado' => 'Pagado',
            'enviado' => 'Enviado',
            'completado' => 'Completado',
            'cancelado' => 'Cancelado'
        ];
        
        return $estados[$estado] ?? $estado;
    }
}

