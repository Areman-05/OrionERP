<?php

namespace OrionERP\Services;

use OrionERP\Services\NotificacionService;
use OrionERP\Services\EmailService;

class TareaProgramadaService
{
    private $notificacionService;
    private $emailService;

    public function __construct()
    {
        $this->notificacionService = new NotificacionService();
        $this->emailService = new EmailService();
    }

    public function ejecutarTareasDiarias(): void
    {
        // Verificar stock bajo
        $this->notificacionService->notificarStockBajo();
        
        // Verificar facturas vencidas
        $this->notificacionService->notificarFacturasPendientes();
    }

    public function enviarResumenDiario(string $emailDestinatario): bool
    {
        $resumen = $this->generarResumenDiario();
        
        $mensaje = "<h2>Resumen Diario - OrionERP</h2>";
        $mensaje .= "<p><strong>Fecha:</strong> " . date('d/m/Y') . "</p>";
        $mensaje .= "<ul>";
        $mensaje .= "<li>Ventas del día: " . number_format($resumen['ventas'], 2) . " €</li>";
        $mensaje .= "<li>Productos con stock bajo: " . $resumen['stock_bajo'] . "</li>";
        $mensaje .= "<li>Pedidos pendientes: " . $resumen['pedidos_pendientes'] . "</li>";
        $mensaje .= "</ul>";
        
        return $this->emailService->enviar($emailDestinatario, 'Resumen Diario - OrionERP', $mensaje);
    }

    private function generarResumenDiario(): array
    {
        $db = \OrionERP\Core\Database::getInstance();
        
        $ventas = $db->fetchOne(
            "SELECT SUM(total) as total FROM pedidos_venta 
             WHERE DATE(fecha) = CURDATE() AND estado != 'cancelado'"
        );
        
        $stockBajo = $db->fetchOne(
            "SELECT COUNT(*) as total FROM productos 
             WHERE stock_actual <= stock_minimo AND activo = 1"
        );
        
        $pedidosPendientes = $db->fetchOne(
            "SELECT COUNT(*) as total FROM pedidos_venta WHERE estado = 'pendiente'"
        );
        
        return [
            'ventas' => (float) ($ventas['total'] ?? 0),
            'stock_bajo' => (int) ($stockBajo['total'] ?? 0),
            'pedidos_pendientes' => (int) ($pedidosPendientes['total'] ?? 0)
        ];
    }
}

