<?php

namespace OrionERP\Controllers;

use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use OrionERP\Services\EstadisticasService;
use OrionERP\Services\NotificacionService;

class DashboardController
{
    private $estadisticasService;
    private $notificacionService;

    public function __construct()
    {
        $this->estadisticasService = new EstadisticasService();
        $this->notificacionService = new NotificacionService();
    }

    public function index(Request $request, Response $response): Response
    {
        $usuarioId = $request->getAttribute('usuario_id', 0);
        
        $kpis = $this->estadisticasService->getKPIs();
        $ventasMes = $this->estadisticasService->getVentasPorMes(date('Y'));
        $productosMasVendidos = $this->estadisticasService->getProductosMasVendidos(5);
        $notificaciones = $usuarioId > 0 ? $this->notificacionService->getNoLeidas($usuarioId) : [];
        
        $response->getBody()->write(json_encode([
            'success' => true,
            'data' => [
                'kpis' => $kpis,
                'ventas_mes' => $ventasMes,
                'productos_mas_vendidos' => $productosMasVendidos,
                'notificaciones' => $notificaciones
            ]
        ]));
        
        return $response->withHeader('Content-Type', 'application/json');
    }
}

