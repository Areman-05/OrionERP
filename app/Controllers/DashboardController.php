<?php

namespace OrionERP\Controllers;

use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use OrionERP\Services\EstadisticasService;
use OrionERP\Services\NotificacionService;
use OrionERP\Services\DashboardService;

class DashboardController
{
    private $estadisticasService;
    private $notificacionService;
    private $dashboardService;

    public function __construct()
    {
        $this->estadisticasService = new EstadisticasService();
        $this->notificacionService = new NotificacionService();
        $this->dashboardService = new DashboardService();
    }

    public function index(Request $request, Response $response): Response
    {
        $usuarioId = $request->getAttribute('usuario_id', 0);
        
        $resumen = $this->dashboardService->getResumenGeneral();
        $kpis = $this->estadisticasService->getKPIs();
        $ventasMes = $this->estadisticasService->getVentasPorMes(date('Y'));
        $ventasUltimosMeses = $this->dashboardService->getVentasUltimosMeses(6);
        $productosMasVendidos = $this->estadisticasService->getProductosMasVendidos(5);
        $notificaciones = $usuarioId > 0 ? $this->notificacionService->getNoLeidas($usuarioId) : [];
        
        $response->getBody()->write(json_encode([
            'success' => true,
            'data' => [
                'resumen' => $resumen,
                'kpis' => $kpis,
                'ventas_mes' => $ventasMes,
                'ventas_ultimos_meses' => $ventasUltimosMeses,
                'productos_mas_vendidos' => $productosMasVendidos,
                'notificaciones' => $notificaciones
            ]
        ]));
        
        return $response->withHeader('Content-Type', 'application/json');
    }
}

